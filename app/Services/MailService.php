<?php

namespace App\Services;

use App\Enums\OtpTokenType;
use App\Jobs\ProcessReverificationEmailJob;
use App\Mail\CompanyWelcomeMail;
use App\Mail\ReverifyEmailMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class MailService
{
    /**
     * Fetch unverified users within the last 2 weeks and send emails in chunks of 30.
     */
    public function sendBulkReverification(): void
    {
        User::whereNull('email_verified_at')
            ->where('created_at', '>=', now()->subWeeks(2))
            ->where(function ($query) {
                // Only send if it hasn't been sent yet, or establish a retry interval
                $query->whereNull('send_unverified_mail_at')
                    ->orWhere('send_unverified_mail_at', '<=', now()->subDays(7)); // after a week
            })
            // Fetch 30 records at a time
            ->limit(30)
            ->get()
            ->each(function (User $user) {
                // $this->sendSingleReverification($user);
                // Dispatch each email safely to your background workers
                ProcessReverificationEmailJob::dispatch($user);
                Log::info('Mail Service - ProcessReverificationEmailJob dispatch', [
                    'user_id' => $user?->id,
                ]);
            });
    }

    /**
     * Send email to a single user and update their timestamp.
     */
    public function sendSingleReverification(User $user): void
    {
        $otp = $this->getOtpCode($user);
        try {
            Log::info('Mail service - send mail:', [
                'user_id' => $user?->id,
                'user_email' => $user?->email,
            ]);
            Mail::to($user->email)->send(new ReverifyEmailMail($user, $otp['otp']));

            $user->update([
                'send_unverified_mail_at' => now(),
            ]);
        } catch (\Exception $exception) {
            // Log errors here safely without crashing the background thread
            Log::error("Mail : Failed to send reverify email to User ID {$user->id} - User email {$user?->email}: ".$exception->getMessage());
        }
    }

    private function getOtpCode(User $user, $type = OtpTokenType::ACCOUNT_VERIFICATION->value, $expiresInMinute = 10)
    {
        $otpTokenService = new OtpTokenService;

        return $otpTokenService->getOtp($user, $type, $expiresInMinute);
    }

    /**
     * Send welcome mail to a user.
     */
    public function sendCompanyWelcomeMail(User $user): void
    {
        try {
            Log::info('Mail service - send welcome mail:', [
                'user_id' => $user?->id,
                'user_email' => $user?->email,
            ]);
            Mail::to($user->email)->send(new CompanyWelcomeMail($user));
        } catch (\Exception $exception) {
            // Log errors here safely without crashing the background thread
            Log::error("Mail : Failed to send welcome mail to User ID {$user?->id} - User email {$user?->email}: ".$exception->getMessage());
        }
    }

    // 'invited_by' => 'Adam Smith',
    // 'company' => App\Models\Company::inRandomOrder()->first(),
    // 'invitee' => App\Models\User::inRandomOrder()->first(),
    // 'invitation_link' => config('app.frontend_url') . '/verifyOtp?email=' . $user?->email . '&otp=' . random_int(111111, 999999),

    // NOTE: sendCompanyInvitationMail() is deactivated because the Company domain
    // (App\Models\Company) is not present in this codebase.

    // send reminder mail [7 days, 3 days 2 days]
    // send final mail [1 day, -2 days, -3 days]
    // send cancellation / downgrade mail [- 7 days]
}
