<?php

namespace App\Services;

use App\Enums\OtpTokenType;
use App\Mail\ForgetPasswordMail;
use App\Mail\TwoFactorAuthMail;
use App\Mail\VerifyAccountMail;
use App\Models\OtpToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OtpTokenService
{
    public const int MAX_VERIFY_ATTEMPTS = 5;

    protected function generateOtp(): int
    {
        return random_int(100000, 999999);
    }

    protected function getToken($type = null, $user = null, $expiresInMinute = 10): array
    {
        $user ??= request()->user();

        $type ??= OtpTokenType::ACCOUNT_VERIFICATION->value;
        Log::info('OtpTokenService - generating token for user: '.$user->id.' type: '.$type);

        // Delete previous tokens of the same type and any expired tokens
        OtpToken::where('user_id', $user->id)
            ->where(function ($query) use ($type) {
                $query->where('type', $type)
                    ->orWhere('expires', '<', now());
            })
            ->delete();

        $otp = $this->generateOtp();
        $expires = Carbon::now()->addMinutes($expiresInMinute);
        $token = OtpToken::create([
            'user_id' => $user->id,
            'type' => $type,
            'token' => Hash::make($otp),
            'expires' => $expires,
        ]);

        $data = [
            'otp' => $otp,
            'token' => $token,
            'expires' => $expiresInMinute,
        ];

        if (! app()->isProduction()) {
            Log::info('APP IS LOCAL - OTP: ', [$data]);
        }

        return $data;
    }

    /**
     * Send verification otp token to users
     */
    public function sendToken($user, $type = OtpTokenType::ACCOUNT_VERIFICATION->value)
    {
        Log::info('OtpTokenService - sending token to user: '.$user->id.' type: '.$type);
        $theToken = $this->getToken($type, $user);

        $mail = $type === OtpTokenType::TWO_FACTOR_AUTHENTICATION->value
            ? new TwoFactorAuthMail($user, $theToken['otp'])
            : new VerifyAccountMail($user, $theToken['otp']);

        Mail::to($user->email)->queue($mail);
    }

    /**
     * Get user generate OTP Token from service saved to database
     */
    public function getOtp($user, $type = OtpTokenType::ACCOUNT_VERIFICATION->value, $expiresInMinute = 10)
    {
        // $type = OtpTokenType::ACCOUNT_VERIFICATION->value;
        Log::info('OTP token service:', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'type:' => $type,
        ]);
        $theToken = $this->getToken($type, $user);

        return $theToken ?? null;
    }

    /**
     * Verify otp with user generate OTP and delete it from database
     */
    public function verifyToken($user, $otp, $type = OtpTokenType::ACCOUNT_VERIFICATION->value): bool
    {
        // $type = $type ?? OtpTokenType::ACCOUNT_VERIFICATION->value;

        Log::info('Verify Token:', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'type:' => $type,
        ]);

        $theToken = OtpToken::where('user_id', $user?->id)
            ->where('type', $type)
            ->where('expires', '>=', now())
            ->latest()
            ->first();

        Log::info('OTP verified token check: ', [$user, $otp, $type, $theToken]);

        if (! $theToken) {
            Log::error('OTP verified token failed (no active token): ', [$user, $type]);

            return false;
        }

        if (! Hash::check($otp, $theToken['token'])) {
            $theToken->increment('failed_attempts');

            if ($theToken->failed_attempts >= self::MAX_VERIFY_ATTEMPTS) {
                $theToken->delete();
                Log::warning('OTP verification token revoked after max failed attempts: ', [
                    'user_id' => $user?->id,
                    'type:' => $type,
                    'token_id' => $theToken?->id,
                ]);
            } else {
                Log::error('OTP verified token failed: ', [$user, $otp, $type, $theToken]);
            }

            return false;
        }

        $theToken->delete();
        Log::info('OTP verified token successful: ', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'type:' => $type,
            'token_id' => $theToken?->id,
        ]);

        return true;
    }

    /**
     * Send forgot password token and email to user
     */
    public function sendForgetPasswordToken($user)
    {
        $type = OtpTokenType::FORGET_PASSWORD->value;
        Log::info('Send forget password otp token:', [
            'user_id' => $user?->id,
            'user_email' => $user?->email,
            'type:' => $type,
        ]);
        $theToken = $this->getToken($type, $user);

        // Send account verification
        Mail::to($user->email)->queue(new ForgetPasswordMail($user, $theToken['otp']));
    }
}
