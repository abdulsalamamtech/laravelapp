<?php

use App\Enums\EmailChangeCancelledReason;
use App\Enums\EmailChangeStatus;
use App\Mail\EmailChangeAcceptanceMail;
use App\Mail\EmailChangeCompletedMail;
use App\Mail\EmailChangeConfirmationMail;
use App\Mail\EmailChangeRequestMail;
use App\Mail\EmailChangeWindowMail;
use App\Models\PendingEmailChange;
use App\Models\User;
use App\Services\EmailChangeService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

beforeEach(function () {
    Cache::flush();
    Mail::fake();
});

afterEach(function () {
    auth()->forgetGuards();
});

function storedTokenId($session): string
{
    return (string) DB::table('personal_access_tokens')->where('token', $session->accessToken->token)->value('id');
}

/**
 * Capture the OTP from a queued mailable that exposes a public $otp property.
 */
function captureOtpFromQueuedMail(string $mailClass, string $prop = 'otp'): string
{
    $otp = null;
    Mail::assertQueued($mailClass, function ($mail) use (&$otp, $prop): true {
        $otp = $mail->{$prop} ?? null;

        return true;
    });

    return (string) $otp;
}

describe('Email change - initiate', function () {
    it('creates a pending change, logs the audit fields and emails both inboxes', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ])->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.pending_email', 'new@example.com');

        $this->assertDatabaseHas('pending_email_changes', [
            'user_id' => $user->id,
            'old_email' => 'old@example.com',
            'pending_email' => 'new@example.com',
            'status' => 'pending',
        ]);

        $pending = PendingEmailChange::first();
        expect($pending->initiated_at)->not->toBeNull()
            ->and((int) $pending->initiated_at->diffInMinutes($pending->effective_at))->toBe(1440)
            ->and($pending->status)->toBe(EmailChangeStatus::PENDING);

        Mail::assertQueued(EmailChangeRequestMail::class, fn ($mail) => $mail->hasTo('old@example.com'));
        Mail::assertQueued(EmailChangeConfirmationMail::class, fn ($mail) => $mail->hasTo('new@example.com'));
    });

    it('rejects the current email as the pending email', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'old@example.com',
        ])->assertStatus(422);
    });

    it('rejects an email already used by another account', function () {
        User::factory()->create(['email' => 'taken@example.com']);
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'taken@example.com',
        ])->assertStatus(422);
    });

    it('blocks a second change while one is pending', function () {
        $user = User::factory()->create();
        PendingEmailChange::factory()->forUser($user)->create();
        Mail::assertNothingSent();

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'another@example.com',
        ])->assertStatus(409);
    });
});

describe('Email change - confirmations', function () {
    it('only confirming the old inbox does not schedule the change', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ]);
        $oldOtp = captureOtpFromQueuedMail(EmailChangeRequestMail::class);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change/confirm-old', [
            'pending_email' => 'new@example.com',
            'otp' => $oldOtp,
        ])->assertStatus(200)->assertJsonPath('data.status', 'pending');

        expect(PendingEmailChange::first()->old_confirmed_at)->not->toBeNull()
            ->and(PendingEmailChange::first()->status)->toBe(EmailChangeStatus::PENDING);
    });

    it('schedules the change once both inboxes confirm and emails the window + acceptance mails', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);
        $session = $user->createToken('confirm-session');

        $this->withToken($session->plainTextToken)->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ]);
        $oldOtp = captureOtpFromQueuedMail(EmailChangeRequestMail::class);
        $newOtp = captureOtpFromQueuedMail(EmailChangeConfirmationMail::class);

        $this->withToken($session->plainTextToken)->postJson('/api/v1/email/change/confirm-old', [
            'pending_email' => 'new@example.com',
            'otp' => $oldOtp,
        ]);

        $this->withToken($session->plainTextToken)->postJson('/api/v1/email/change/confirm-new', [
            'pending_email' => 'new@example.com',
            'otp' => $newOtp,
        ])->assertStatus(200)
            ->assertJsonPath('data.status', 'scheduled');

        $pending = PendingEmailChange::first();
        expect($pending->status)->toBe(EmailChangeStatus::SCHEDULED)
            ->and($pending->old_confirmed_at)->not->toBeNull()
            ->and($pending->new_confirmed_at)->not->toBeNull()
            ->and($pending->confirming_token_id)->toBe(storedTokenId($session));

        Mail::assertQueued(EmailChangeWindowMail::class, fn ($mail) => $mail->hasTo('old@example.com'));
        Mail::assertQueued(EmailChangeAcceptanceMail::class, fn ($mail) => $mail->hasTo('new@example.com'));
    });

    it('rejects a wrong confirmation code', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ]);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change/confirm-old', [
            'pending_email' => 'new@example.com',
            'otp' => '000000',
        ])->assertStatus(401)->assertJsonPath('success', false);
    });
});

describe('Email change - acceptance and application', function () {
    it('accepts the new inbox and applies the change after the window closes', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);
        $session = $user->createToken('device-a');
        $otherToken = $user->createToken('device-b');

        $this->withToken($session->plainTextToken)->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ]);
        $oldOtp = captureOtpFromQueuedMail(EmailChangeRequestMail::class);
        $newOtp = captureOtpFromQueuedMail(EmailChangeConfirmationMail::class);

        $this->withToken($session->plainTextToken)->postJson('/api/v1/email/change/confirm-old', ['pending_email' => 'new@example.com', 'otp' => $oldOtp]);
        $this->withToken($session->plainTextToken)->postJson('/api/v1/email/change/confirm-new', ['pending_email' => 'new@example.com', 'otp' => $newOtp]);
        $acceptOtp = captureOtpFromQueuedMail(EmailChangeAcceptanceMail::class);

        $this->postJson('/api/v1/email/change/accept', [
            'pending_email' => 'new@example.com',
            'otp' => $acceptOtp,
        ])->assertStatus(200)->assertJsonPath('data.status', 'scheduled');

        $pending = PendingEmailChange::first();
        expect($pending->accepted_at)->not->toBeNull()
            ->and($pending->confirming_token_id)->toBe(storedTokenId($session));

        $this->travelTo($pending->effective_at->addMinute());
        app(EmailChangeService::class)->applyDue();

        $pending->refresh();
        expect($pending->status)->toBe(EmailChangeStatus::APPLIED)
            ->and($pending->changed_at)->not->toBeNull();

        $user->refresh();
        expect($user->email)->toBe('new@example.com')
            ->and($user->email_verified_at)->not->toBeNull();

        Mail::assertQueued(EmailChangeCompletedMail::class, fn ($mail) => $mail->hasTo('new@example.com'));

        // only the confirming device token remains
        expect($user->tokens()->count())->toBe(1)
            ->and((string) $user->tokens()->first()->id)->toBe($pending->confirming_token_id);
        expect($otherToken->accessToken->fresh())->toBeNull();
    });

    it('expires a scheduled change that was never accepted once the window closes', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ]);
        $oldOtp = captureOtpFromQueuedMail(EmailChangeRequestMail::class);
        $newOtp = captureOtpFromQueuedMail(EmailChangeConfirmationMail::class);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change/confirm-old', ['pending_email' => 'new@example.com', 'otp' => $oldOtp]);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change/confirm-new', ['pending_email' => 'new@example.com', 'otp' => $newOtp]);
        $pending = PendingEmailChange::first();
        expect($pending->status)->toBe(EmailChangeStatus::SCHEDULED);

        $this->travelTo($pending->effective_at->addMinute());
        app(EmailChangeService::class)->applyDue();
        app(EmailChangeService::class)->expireStale();

        $pending->refresh();
        expect($pending->status)->toBe(EmailChangeStatus::EXPIRED);

        $user->refresh();
        expect($user->email)->toBe('old@example.com');
    });
});

describe('Email change - cancel and report', function () {
    it('cancels a pending change with the current-inbox code', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ]);
        $oldOtp = captureOtpFromQueuedMail(EmailChangeRequestMail::class);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change/cancel', [
            'pending_email' => 'new@example.com',
            'otp' => $oldOtp,
        ])->assertStatus(200)->assertJsonPath('data.status', 'cancelled');

        $pending = PendingEmailChange::first();
        expect($pending->status)->toBe(EmailChangeStatus::CANCELLED)
            ->and($pending->cancelled_reason)->toBe(EmailChangeCancelledReason::OWNER_CANCEL)
            ->and($pending->cancelled_at)->not->toBeNull();

        $user->refresh();
        expect($user->email)->toBe('old@example.com');
    });

    it('reports suspicious activity after scheduling, retains the email and revokes every session', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);
        $user->createToken('device-a');
        $user->createToken('device-b');

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ]);
        $oldOtp = captureOtpFromQueuedMail(EmailChangeRequestMail::class);
        $newOtp = captureOtpFromQueuedMail(EmailChangeConfirmationMail::class);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change/confirm-old', ['pending_email' => 'new@example.com', 'otp' => $oldOtp]);
        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change/confirm-new', ['pending_email' => 'new@example.com', 'otp' => $newOtp]);
        $reportOtp = captureOtpFromQueuedMail(EmailChangeWindowMail::class, 'reportOtp');

        $this->postJson('/api/v1/email/change/report', [
            'pending_email' => 'new@example.com',
            'otp' => $reportOtp,
        ])->assertStatus(200)->assertJsonPath('data.status', 'cancelled');

        $pending = PendingEmailChange::first();
        expect($pending->status)->toBe(EmailChangeStatus::CANCELLED)
            ->and($pending->cancelled_reason)->toBe(EmailChangeCancelledReason::SUSPICIOUS_REPORT);

        $user->refresh();
        expect($user->email)->toBe('old@example.com')
            ->and($user->tokens()->count())->toBe(0);
    });

    it('rejects a report without the correct code', function () {
        $user = User::factory()->create(['email' => 'old@example.com']);

        $this->actingAs($user, 'sanctum')->postJson('/api/v1/email/change', [
            'pending_email' => 'new@example.com',
        ]);

        $this->postJson('/api/v1/email/change/report', [
            'pending_email' => 'new@example.com',
            'otp' => '000000',
        ])->assertStatus(401);
    });
});
