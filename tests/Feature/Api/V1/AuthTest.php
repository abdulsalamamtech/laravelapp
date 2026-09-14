<?php

use App\Enums\OtpTokenType;
use App\Mail\CompanyWelcomeMail;
use App\Mail\ForgetPasswordMail;
use App\Mail\TwoFactorAuthMail;
use App\Mail\VerifyAccountMail;
use App\Models\OtpToken;
use App\Models\User;
use App\Services\OtpTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

uses(RefreshDatabase::class);

// Keep the rate limiter clean between tests so throttled routes don't bleed over.
beforeEach(function () {
    Cache::flush();
});

// The sanctum guard caches state between requests within a test; drop it afterwards.
afterEach(function () {
    auth()->forgetGuards();
});

function makeUser(array $overrides = [])
{
    return User::factory()->create($overrides);
}

function createOtpFor(User $user, string $type, string $otp = '123456', ?Carbon\Carbon $expires = null): OtpToken
{
    return OtpToken::create([
        'user_id' => $user->id,
        'type' => $type,
        'token' => Hash::make($otp),
        'expires' => $expires ?? now()->addMinutes(10),
    ]);
}

function captureOtpFromMail(string $mailClass): string
{
    $otp = null;
    Mail::assertQueued($mailClass, function ($mail) use (&$otp): true {
        $otp = $mail->otp;

        return true;
    });

    return (string) $otp;
}

describe('Register', function () {
    it('registers a user, creates an otp row and queues a verification mail without issuing a token', function () {
        Mail::fake();

        $response = $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms_and_condition' => true,
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', 'jane@example.com')
            ->assertJsonMissingPath('token')
            ->assertJsonMissingPath('data.token');

        $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
        $user = User::where('email', 'jane@example.com')->first();
        $this->assertDatabaseHas('otp_tokens', [
            'user_id' => $user->id,
            'type' => OtpTokenType::ACCOUNT_VERIFICATION->value,
        ]);

        Mail::assertQueued(VerifyAccountMail::class);
    });

    it('fails when the email is already taken', function () {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'taken@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'terms_and_condition' => true,
        ])->assertStatus(422)->assertJsonValidationErrors('email');
    });

    it('fails when terms and conditions are not accepted', function () {
        $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ])->assertStatus(422)->assertJsonValidationErrors('terms_and_condition');
    });

    it('fails when the password is too short', function () {
        $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
            'terms_and_condition' => true,
        ])->assertStatus(422)->assertJsonValidationErrors('password');
    });

    it('fails when the password confirmation does not match', function () {
        $this->postJson('/api/v1/register', [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different123',
            'terms_and_condition' => true,
        ])->assertStatus(422)->assertJsonValidationErrors('password');
    });
});

describe('Login', function () {
    it('logs in a verified user and returns a bearer token', function () {
        makeUser();

        $this->postJson('/api/v1/login', [
            'email' => User::first()->email,
            'password' => 'password',
        ])->assertStatus(200)
            ->assertJsonStructure(['success', 'message', 'data' => ['token', 'user']]);
    });

    it('fails with an incorrect password', function () {
        $user = makeUser();

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(401)->assertJsonPath('success', false);
    });

    it('fails for an unknown email', function () {
        $this->postJson('/api/v1/login', [
            'email' => 'nobody@example.com',
            'password' => 'password',
        ])->assertStatus(401)->assertJsonPath('success', false);
    });

    it('blocks unverified users from logging in', function () {
        $user = User::factory()->unverified()->create();

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(403)
            ->assertJsonPath('errors', 'EMAIL_NOT_VERIFIED');
    });

    it('sends a two factor otp instead of a token when 2fa is enabled', function () {
        Mail::fake();
        $user = User::factory()->create(['two_factor_auth' => 'enable']);

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(200)
            ->assertJsonPath('message', 'OTP sent to your email for two-factor authentication.')
            ->assertJsonMissingPath('token')
            ->assertJsonMissingPath('data.token');

        $this->assertDatabaseHas('otp_tokens', [
            'user_id' => $user->id,
            'type' => OtpTokenType::TWO_FACTOR_AUTHENTICATION->value,
        ]);
        Mail::assertQueued(TwoFactorAuthMail::class);
    });

    it('is throttled after repeated attempts', function () {
        $user = User::factory()->unverified()->create();

        foreach (range(1, 6) as $attempt) {
            $this->postJson('/api/v1/login', [
                'email' => $user->email,
                'password' => 'wrong-password',
            ]);
        }

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertStatus(429);
    });
});

describe('Verify Account', function () {
    it('verifies the account with a valid otp, returns a token and consumes the otp', function () {
        Mail::fake();
        $user = User::factory()->unverified()->create();
        createOtpFor($user, OtpTokenType::ACCOUNT_VERIFICATION->value);

        $response = $this->postJson('/api/v1/verify-account', [
            'email' => $user->email,
            'otp' => '123456',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.user.email', $user->email)
            ->assertJsonStructure(['data' => ['token']]);

        $this->assertNotNull($user->fresh()->email_verified_at);
        $this->assertDatabaseMissing('otp_tokens', ['user_id' => $user->id]);
        Mail::assertSent(CompanyWelcomeMail::class);
    });

    it('fails with an invalid otp and keeps the otp valid', function () {
        $user = User::factory()->unverified()->create();
        $otp = createOtpFor($user, OtpTokenType::ACCOUNT_VERIFICATION->value);

        $this->postJson('/api/v1/verify-account', [
            'email' => $user->email,
            'otp' => '000000',
        ])->assertStatus(401);

        $this->assertNull($user->fresh()->email_verified_at);
        $this->assertNotNull($otp->fresh());
    });

    it('fails with an expired otp', function () {
        $user = User::factory()->unverified()->create();
        createOtpFor($user, OtpTokenType::ACCOUNT_VERIFICATION->value, expires: now()->subMinute());

        $this->postJson('/api/v1/verify-account', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(401);

        $this->assertNull($user->fresh()->email_verified_at);
    });

    it('fails when the email does not exist', function () {
        $this->postJson('/api/v1/verify-account', [
            'email' => 'nobody@example.com',
            'otp' => '123456',
        ])->assertStatus(401);
    });

    it('informs a verified user that the email is already verified', function () {
        $user = makeUser();

        $this->postJson('/api/v1/verify-account', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(200)->assertJsonPath('message', 'Email already verified');
    });

    it('only allows an otp to be used once and then considers the email verified', function () {
        $user = User::factory()->unverified()->create();
        createOtpFor($user, OtpTokenType::ACCOUNT_VERIFICATION->value);

        $this->postJson('/api/v1/verify-account', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(200);

        // OTP was consumed.
        $this->assertDatabaseMissing('otp_tokens', ['user_id' => $user->id]);

        // The account is now verified, so the same code is no longer actionable.
        $this->postJson('/api/v1/verify-account', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(200)->assertJsonPath('message', 'Email already verified');
    });

    it('revokes the otp after reaching the max failed attempts', function () {
        $user = User::factory()->unverified()->create();
        createOtpFor($user, OtpTokenType::ACCOUNT_VERIFICATION->value);

        foreach (range(1, OtpTokenService::MAX_VERIFY_ATTEMPTS) as $attempt) {
            $this->postJson('/api/v1/verify-account', [
                'email' => $user->email,
                'otp' => '000000',
            ])->assertStatus(401);
        }

        $this->assertDatabaseMissing('otp_tokens', ['user_id' => $user->id]);
    });

    it('resends a verification code replacing the previous otp', function () {
        Mail::fake();
        $user = User::factory()->unverified()->create();
        createOtpFor($user, OtpTokenType::ACCOUNT_VERIFICATION->value);

        $this->postJson('/api/v1/resend-token', [
            'email' => $user->email,
        ])->assertStatus(200);

        $this->assertDatabaseCount('otp_tokens', 1);
        Mail::assertQueued(VerifyAccountMail::class);

        // The newly issued code works, the replaced one does not.
        $freshOtp = captureOtpFromMail(VerifyAccountMail::class);
        $this->postJson('/api/v1/verify-account', [
            'email' => $user->email,
            'otp' => $freshOtp,
        ])->assertStatus(200);
    });

    it('refuses to resend a verification code for an already verified email', function () {
        $user = makeUser();

        $this->postJson('/api/v1/resend-token', [
            'email' => $user->email,
        ])->assertStatus(409);
    });
});

describe('Forgot & Confirm Password', function () {
    it('forgot password creates an otp and queues a mail', function () {
        Mail::fake();
        $user = makeUser();

        $this->postJson('/api/v1/forgot-password', [
            'email' => $user->email,
        ])->assertStatus(200)->assertJsonPath('success', true);

        $this->assertDatabaseHas('otp_tokens', [
            'user_id' => $user->id,
            'type' => OtpTokenType::FORGET_PASSWORD->value,
        ]);
        Mail::assertQueued(ForgetPasswordMail::class);
    });

    it('forgot password responds identically for unknown emails to avoid enumeration', function () {
        Mail::fake();

        $this->postJson('/api/v1/forgot-password', [
            'email' => 'nobody@example.com',
        ])->assertStatus(200)->assertJsonPath('success', true);

        Mail::assertNothingQueued();
    });

    it('confirms a new password with a valid otp', function () {
        $user = makeUser();
        createOtpFor($user, OtpTokenType::FORGET_PASSWORD->value);

        $this->postJson('/api/v1/confirm-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'brandnewpassword',
            'password_confirmation' => 'brandnewpassword',
        ])->assertStatus(200)->assertJsonPath('success', true);

        $this->assertDatabaseMissing('otp_tokens', ['user_id' => $user->id]);

        // Old password no longer works, new one does.
        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(401);

        $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'brandnewpassword',
        ])->assertStatus(200);
    });

    it('fails confirm password with an invalid otp', function () {
        $user = makeUser();
        createOtpFor($user, OtpTokenType::FORGET_PASSWORD->value);

        $this->postJson('/api/v1/confirm-password', [
            'email' => $user->email,
            'otp' => '000000',
            'password' => 'brandnewpassword',
            'password_confirmation' => 'brandnewpassword',
        ])->assertStatus(401)->assertJsonPath('success', false);
    });

    it('fails confirm password with an expired otp', function () {
        $user = makeUser();
        createOtpFor($user, OtpTokenType::FORGET_PASSWORD->value, expires: now()->subMinute());

        $this->postJson('/api/v1/confirm-password', [
            'email' => $user->email,
            'otp' => '123456',
            'password' => 'brandnewpassword',
            'password_confirmation' => 'brandnewpassword',
        ])->assertStatus(401);
    });

    it('fails confirm password for an unknown email', function () {
        $this->postJson('/api/v1/confirm-password', [
            'email' => 'nobody@example.com',
            'otp' => '123456',
            'password' => 'brandnewpassword',
            'password_confirmation' => 'brandnewpassword',
        ])->assertStatus(422);
    });
});

describe('Two Factor Authentication', function () {
    it('verifies the two factor otp and issues a token', function () {
        $user = User::factory()->create(['two_factor_auth' => 'enable']);
        createOtpFor($user, OtpTokenType::TWO_FACTOR_AUTHENTICATION->value);

        $this->postJson('/api/v1/verify-two-factor-otp', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure(['data' => ['token', 'user']]);

        $this->assertDatabaseMissing('otp_tokens', ['user_id' => $user->id]);
    });

    it('fails two factor verification with an invalid otp', function () {
        $user = User::factory()->create(['two_factor_auth' => 'enable']);
        createOtpFor($user, OtpTokenType::TWO_FACTOR_AUTHENTICATION->value);

        $this->postJson('/api/v1/verify-two-factor-otp', [
            'email' => $user->email,
            'otp' => '000000',
        ])->assertStatus(401)->assertJsonPath('success', false);
    });

    it('fails two factor verification when 2fa is not enabled', function () {
        $user = makeUser();

        $this->postJson('/api/v1/verify-two-factor-otp', [
            'email' => $user->email,
            'otp' => '123456',
        ])->assertStatus(403);
    });

    it('fails two factor verification for an unknown email', function () {
        $this->postJson('/api/v1/verify-two-factor-otp', [
            'email' => 'nobody@example.com',
            'otp' => '123456',
        ])->assertStatus(401);
    });

    it('resends a two factor otp', function () {
        Mail::fake();
        $user = User::factory()->create(['two_factor_auth' => 'enable']);

        $this->postJson('/api/v1/resend-two-factor-otp', [
            'email' => $user->email,
        ])->assertStatus(200);

        $this->assertDatabaseHas('otp_tokens', [
            'user_id' => $user->id,
            'type' => OtpTokenType::TWO_FACTOR_AUTHENTICATION->value,
        ]);
        Mail::assertQueued(TwoFactorAuthMail::class);
    });

    it('refuses to resend a two factor otp when 2fa is not enabled', function () {
        $user = makeUser();

        $this->postJson('/api/v1/resend-two-factor-otp', [
            'email' => $user->email,
        ])->assertStatus(401)->assertJsonPath('success', false);
    });
});

describe('Authenticated user', function () {
    function authHeaders(User $user): array
    {
        return ['Authorization' => 'Bearer '.$user->createToken('test-token')->plainTextToken];
    }

    it('resets the password with a valid current password', function () {
        $user = makeUser();

        $this->withHeaders(authHeaders($user))
            ->postJson('/api/v1/reset-password', [
                'current_password' => 'password',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ])->assertStatus(200)->assertJsonPath('success', true);

        expect(Hash::check('password', $user->fresh()->password))->toBeFalse();
        expect(Hash::check('newpassword123', $user->fresh()->password))->toBeTrue();
    });

    it('fails to reset the password with a wrong current password', function () {
        $user = makeUser();

        $this->withHeaders(authHeaders($user))
            ->postJson('/api/v1/reset-password', [
                'current_password' => 'wrong-password',
                'new_password' => 'newpassword123',
                'new_password_confirmation' => 'newpassword123',
            ])->assertStatus(422)->assertJsonValidationErrors('current_password');
    });

    it('logs out and invalidates the current token', function () {
        $user = makeUser();
        $token = $user->createToken('test-token')->plainTextToken;

        $this->withHeaders(['Authorization' => 'Bearer '.$token])
            ->postJson('/api/v1/logout')
            ->assertStatus(200)->assertJsonPath('success', true);

        $this->assertDatabaseCount('personal_access_tokens', 0);
        expect($user->tokens()->count())->toBe(0);
    });

    it('fails to logout without a token', function () {
        $this->postJson('/api/v1/logout')->assertStatus(401);
    });

    it('logs out all devices by deleting every token', function () {
        $user = makeUser();
        $tokenOne = $user->createToken('one')->plainTextToken;
        $user->createToken('two');

        $this->withHeaders(['Authorization' => 'Bearer '.$tokenOne])
            ->postJson('/api/v1/logout-devices')
            ->assertStatus(200)->assertJsonPath('success', true);

        $this->assertDatabaseCount('personal_access_tokens', 0);
        expect($user->tokens()->count())->toBe(0);
    });

    it('returns the authenticated user from the authenticated endpoint', function () {
        $user = makeUser();

        $this->withHeaders(authHeaders($user))
            ->getJson('/api/v1/authenticated')
            ->assertStatus(200)
            ->assertJsonPath('data.user.email', $user->email);
    });
});
