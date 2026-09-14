<?php

namespace App\Services;

use App\Enums\EmailChangeCancelledReason;
use App\Enums\EmailChangeStatus;
use App\Enums\EmailChangeWindow;
use App\Enums\OtpTokenType;
use App\Helpers\ApiResponse;
use App\Mail\EmailChangeAcceptanceMail;
use App\Mail\EmailChangeCompletedMail;
use App\Mail\EmailChangeConfirmationMail;
use App\Mail\EmailChangeRequestMail;
use App\Mail\EmailChangeWindowMail;
use App\Models\PendingEmailChange;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Mail;

class EmailChangeService
{
    public function __construct(public OtpTokenService $otpTokenService) {}

    /**
     * Start an email change: keep the old address active and schedule the new one.
     */
    public function initiate(User $user, string $pendingEmail, ?string $ip = null, ?string $userAgent = null): PendingEmailChange
    {
        $this->assertEmailIsChangeable($user, $pendingEmail);
        $this->assertNoActiveChange($user);

        $now = now();

        $pending = PendingEmailChange::create([
            'user_id' => $user->id,
            'old_email' => $user->email,
            'pending_email' => $pendingEmail,
            'status' => EmailChangeStatus::PENDING,
            'initiated_at' => $now,
            'effective_at' => $now->copy()->addMinutes(EmailChangeWindow::TWENTY_FOUR_HOURS->toMinutes()),
            'ip_address' => $ip,
            'user_agent' => $userAgent,
        ]);

        $oldOtp = $this->otpTokenService->getOtp($user, OtpTokenType::EMAIL_CHANGE_OLD->value, self::windowMinutes());
        $newOtp = $this->otpTokenService->getOtp($user, OtpTokenType::EMAIL_CHANGE_NEW->value, self::windowMinutes());

        Mail::to($user->email)->queue(new EmailChangeRequestMail($user, $oldOtp['otp'], $pendingEmail));
        Mail::to($pendingEmail)->queue(new EmailChangeConfirmationMail($user, $newOtp['otp'], $pendingEmail));

        return $pending;
    }

    /**
     * Approve the change from the current (old) inbox.
     */
    public function confirmOld(User $user, string $pendingEmail, string $otp): PendingEmailChange
    {
        $pending = $this->activePendingFor($user, $pendingEmail);
        $this->requirePending($pending);
        $this->requireCode($user, $otp, OtpTokenType::EMAIL_CHANGE_OLD->value, 'Invalid or expired code from your current inbox.');

        $pending->update(['old_confirmed_at' => now()]);

        return $this->maybeSchedule($pending);
    }

    /**
     * Verify the new inbox, remember the confirming session, and schedule the change.
     */
    public function confirmNew(User $user, string $pendingEmail, string $otp, ?string $tokenId = null): PendingEmailChange
    {
        $pending = $this->activePendingFor($user, $pendingEmail);
        $this->requirePending($pending);
        $this->requireCode($user, $otp, OtpTokenType::EMAIL_CHANGE_NEW->value, 'Invalid or expired code from your new inbox.');

        $pending->update([
            'new_confirmed_at' => now(),
            'confirming_token_id' => $tokenId,
        ]);

        return $this->maybeSchedule($pending);
    }

    /**
     * The old inbox can cancel a scheduled change before it takes effect.
     */
    public function cancel(User $user, string $pendingEmail, string $otp): PendingEmailChange
    {
        $pending = $this->activePendingFor($user, $pendingEmail);
        $this->requirePending($pending);

        $verified = $this->otpTokenService->verifyToken($user, $otp, OtpTokenType::EMAIL_CHANGE_OLD->value)
            || $this->otpTokenService->verifyToken($user, $otp, OtpTokenType::EMAIL_CHANGE_REPORT->value);

        if (! $verified) {
            throw new HttpResponseException(ApiResponse::error(null, 'Invalid or expired code from your current inbox.', 401));
        }

        $this->markCancelled($pending, EmailChangeCancelledReason::OWNER_CANCEL);

        return $pending->fresh();
    }

    /**
     * The new inbox must explicitly accept the address before the window closes.
     */
    public function accept(string $pendingEmail, string $otp): PendingEmailChange
    {
        $pending = $this->scheduledPendingFor($pendingEmail);
        $this->requirePending($pending);
        $this->requireCode($pending->user, $otp, OtpTokenType::EMAIL_CHANGE_ACCEPT->value, 'Invalid or expired acceptance code.');

        $pending->update(['accepted_at' => now()]);

        return $pending->fresh();
    }

    /**
     * Report a suspicious change from the old inbox: retain the existing email and lock the account.
     */
    public function reportSuspicious(string $pendingEmail, string $otp): PendingEmailChange
    {
        $pending = PendingEmailChange::where('pending_email', $pendingEmail)
            ->whereIn('status', [EmailChangeStatus::PENDING, EmailChangeStatus::SCHEDULED])
            ->latest('initiated_at')
            ->first();
        $this->requirePending($pending);
        $this->requireCode($pending->user, $otp, OtpTokenType::EMAIL_CHANGE_REPORT->value, 'Invalid or expired security report code.');

        $this->markCancelled($pending, EmailChangeCancelledReason::SUSPICIOUS_REPORT);
        $pending->user->tokens()->delete();

        return $pending->fresh();
    }

    /**
     * Apply every accepted, due scheduled change.
     */
    public function applyDue(): int
    {
        $count = 0;

        PendingEmailChange::where('status', EmailChangeStatus::SCHEDULED)
            ->whereNotNull('accepted_at')
            ->where('effective_at', '<=', now())
            ->get()
            ->each(function (PendingEmailChange $pending) use (&$count) {
                $this->apply($pending);
                $count++;
            });

        return $count;
    }

    /**
     * Flip users.email once the change is accepted and the window has passed.
     */
    public function apply(PendingEmailChange $pending): void
    {
        $user = $pending->user;

        if (strcasecmp($user->email, $pending->old_email) !== 0) {
            $this->markCancelled($pending, EmailChangeCancelledReason::OWNER_CANCEL, 'email changed underneath the pending request');

            return;
        }

        $user->update([
            'email' => $pending->pending_email,
            'email_verified_at' => now(),
        ]);

        $pending->update([
            'status' => EmailChangeStatus::APPLIED,
            'changed_at' => now(),
        ]);

        if ($pending->confirming_token_id) {
            $user->tokens()->where('id', '!=', $pending->confirming_token_id)->delete();
        }

        Mail::to([$pending->old_email, $pending->pending_email])
            ->queue(new EmailChangeCompletedMail($user, $pending->old_email, $pending->pending_email));
    }

    /**
     * Expire pending/scheduled changes whose window has closed unanswered.
     */
    public function expireStale(): int
    {
        $count = 0;

        PendingEmailChange::whereIn('status', [EmailChangeStatus::PENDING, EmailChangeStatus::SCHEDULED])
            ->where('effective_at', '<', now())
            ->get()
            ->each(function (PendingEmailChange $pending) use (&$count) {
                $pending->update(['status' => EmailChangeStatus::EXPIRED]);
                $count++;
            });

        return $count;
    }

    /**
     * The configured window (in minutes) valid for email-change codes and the effective date.
     */
    protected static function windowMinutes(): int
    {
        return EmailChangeWindow::TWENTY_FOUR_HOURS->toMinutes();
    }

    protected function maybeSchedule(PendingEmailChange $pending): PendingEmailChange
    {
        $pending = $pending->fresh();

        if ($pending->old_confirmed_at && $pending->new_confirmed_at && $pending->status === EmailChangeStatus::PENDING) {
            $pending->update(['status' => EmailChangeStatus::SCHEDULED]);

            $acceptOtp = $this->otpTokenService->getOtp($pending->user, OtpTokenType::EMAIL_CHANGE_ACCEPT->value, self::windowMinutes());
            $reportOtp = $this->otpTokenService->getOtp($pending->user, OtpTokenType::EMAIL_CHANGE_REPORT->value, self::windowMinutes());

            Mail::to($pending->old_email)->queue(new EmailChangeWindowMail($pending->user, $pending, $reportOtp['otp']));
            Mail::to($pending->pending_email)->queue(new EmailChangeAcceptanceMail($pending->user, $pending->pending_email, $acceptOtp['otp']));
        }

        return $pending->fresh();
    }

    protected function activePendingFor(User $user, string $pendingEmail): ?PendingEmailChange
    {
        return PendingEmailChange::where('user_id', $user->id)
            ->where('pending_email', $pendingEmail)
            ->whereIn('status', [EmailChangeStatus::PENDING, EmailChangeStatus::SCHEDULED])
            ->latest('initiated_at')
            ->first();
    }

    protected function scheduledPendingFor(string $pendingEmail): ?PendingEmailChange
    {
        return PendingEmailChange::where('pending_email', $pendingEmail)
            ->where('status', EmailChangeStatus::SCHEDULED)
            ->latest('initiated_at')
            ->first();
    }

    protected function requirePending(?PendingEmailChange $pending): void
    {
        if (! $pending instanceof PendingEmailChange) {
            throw new HttpResponseException(ApiResponse::error(null, 'No pending email change found for this address.', 404));
        }
    }

    protected function requireCode(User $user, string $otp, string $type, string $message): void
    {
        if (! $this->otpTokenService->verifyToken($user, $otp, $type)) {
            throw new HttpResponseException(ApiResponse::error(null, $message, 401));
        }
    }

    protected function markCancelled(PendingEmailChange $pending, EmailChangeCancelledReason $reason, ?string $note = null): void
    {
        $pending->update([
            'status' => EmailChangeStatus::CANCELLED,
            'cancelled_reason' => $reason,
            'cancelled_at' => now(),
        ]);

        if ($note) {
            activity('user profile')
                ->causedBy($pending->user)
                ->performedOn($pending)
                ->log('Email change cancelled: '.$note);
        }
    }

    protected function assertNoActiveChange(User $user): void
    {
        $hasActive = PendingEmailChange::where('user_id', $user->id)
            ->whereIn('status', [EmailChangeStatus::PENDING, EmailChangeStatus::SCHEDULED])
            ->exists();

        if ($hasActive) {
            throw new HttpResponseException(ApiResponse::error(null, 'An email change is already pending for your account.', 409));
        }
    }

    protected function assertEmailIsChangeable(User $user, string $pendingEmail): void
    {
        if (strcasecmp($user->email, $pendingEmail) === 0) {
            throw new HttpResponseException(ApiResponse::error(null, 'The new email must be different from your current email.', 422));
        }

        $taken = User::whereRaw('lower(email) = ?', [strtolower($pendingEmail)])
            ->where('id', '!=', $user->id)
            ->exists();

        if ($taken) {
            throw new HttpResponseException(ApiResponse::error(null, 'That email address is already in use.', 422));
        }
    }
}
