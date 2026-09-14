<?php

namespace App\Http\Controllers\V1;

use App\Enums\AppRole;
use App\Enums\OtpTokenType;
use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\MailService;
use App\Services\OtpTokenService;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(public OtpTokenService $otpTokenService) {}

    /**
     * Generate Auth Token
     */
    private function generateAuthToken(User $user, int $expiresInHours = 24)
    {
        Log::info('Login token generated: ', [$user]);

        return $user->createToken('auth_token', ['*'], now()->addHours($expiresInHours ?? 24))->plainTextToken;
    }

    /**
     * Register
     *
     * @param name
     * @param email
     * @param password
     * @param terms_and_condition
     */
    public function register(Request $request)
    {
        // Validation
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'city' => 'nullable|string|max:15',
            'state' => 'nullable|string|max:15',
            'country' => 'nullable|string|max:15',
            'phone_number' => 'nullable|string|min:10|max:15',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
            'terms_and_condition' => 'required|boolean|accepted',
        ]);

        try {
            // code...
            DB::beginTransaction();
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'] ?? null,
                'password' => Hash::make($data['password']),
                'app_role' => AppRole::USER,
            ]);
            Log::info('Register - User created: ', [$user]);

            // Send OTP to user email
            $this->otpTokenService->sendToken($user, OtpTokenType::ACCOUNT_VERIFICATION->value);
            Log::info('Register - User verify OTP sent: ', [$user]);

            DB::commit();
        } catch (\Throwable $throwable) {
            // throw $th;
            DB::rollBack();

            Log::error('Register - User registered failed: ', [$throwable->getMessage()]);

            if (app()->isLocal()) {
                return ApiResponse::error([$throwable->getMessage()], 'App Error- User registered failed, please try again later.', 401);
            }

            return ApiResponse::error([], 'User registered failed, please try again later.', 401);
        }

        Log::info('Register - User registration successful: ', [$request->all()]);

        return ApiResponse::success(['user' => $user], 'User registered successfully, check your email to verify your account.');
    }

    /**
     * Test Login
     *
     * @param email
     * @param password
     */
    public function login(Request $request)
    {
        // Validation
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        // Incorrect email or password
        if (! Auth::attempt($request->only('email', 'password'))) {
            Log::error('Login validation failed');

            return ApiResponse::error(null, 'Unauthorized, incorrect email or password', 401);
        }

        $user = Auth::user();

        // Block unverified accounts from logging in
        if (! $user->hasVerifiedEmail()) {
            Log::info('Login blocked - email not verified: ', [$request->all()]);

            return ApiResponse::error('EMAIL_NOT_VERIFIED', 'Your email address has not been verified yet. Please check your inbox.', 403);
        }

        // Check if 2fa is enabled for the user
        if ($user?->two_factor_auth == 'enable') {
            // Generate and send OTP to user's email
            $this->otpTokenService->sendToken($user, OtpTokenType::TWO_FACTOR_AUTHENTICATION->value);

            return ApiResponse::success(['user' => $user], 'OTP sent to your email for two-factor authentication.');
        }

        // $token = $user->createToken('auth_token')->plainTextToken;
        $token = $this->generateAuthToken($user, 24);

        Log::info('User login successful: ', [$request->all()]);

        return ApiResponse::success(['token' => $token, 'user' => $user], 'Login successful.');
    }

    /**
     * Reset password for the currently authenticated user
     *
     * @param current_password
     * @param password
     */
    public function updatePassword(Request $request)
    {
        // Validation
        $data = $request->validate([
            // 'email' => ['required', 'email'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
            'new_password_confirmation' => ['required', 'string'],
            'current_password' => ['required', 'string', 'current_password:sanctum'],
        ]);

        // Update password
        $auth = Auth::user()->update([
            'password' => $data['new_password'],
        ]);
        if (! $auth) {
            Log::error('User update password failed: ', [$request->all()]);

            return ApiResponse::error(null, 'Password reset failed.', 401);
        }

        Log::info('User update password successful: ', [$request->all()]);

        return ApiResponse::success(['user' => $request->user()], 'Password reset successful.');
    }

    /**
     * Forgot password
     *
     * @param email
     */
    public function forgotPassword(Request $request)
    {
        try {
            // Validation
            $validator = Validator::make($request->all(), [
                // vulnerability
                // 'email' => 'required|email|exists:users,email',
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                Log::error('Email verification failed: ', [$request->all()]);

                // return response()->json(['error' => $validator->errors()], 401);
                return ApiResponse::error($validator->errors(), 'Email verification failed', 401);
            }
        } catch (ValidationException $validationException) {
            // throw $e;
            Log::error('Forget password validation exception: ', [$validationException->getMessage()]);

            return ApiResponse::error([], 'Something went wrong, forgot password process failed', 401);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            Log::info('Forget password user not found: ', [$request->all()]);

            // Same response as a found user to avoid account enumeration
            return ApiResponse::success([], 'Check your email inbox for an OTP to setup your new password');
        }

        // Send otp to user
        $this->otpTokenService->sendForgetPasswordToken($user);
        Log::info('Forget password user ot sent: ', [$request->all()]);

        return ApiResponse::success([], 'Check your email inbox for an OTP to setup your new password');
    }

    /**
     * Handle an incoming forget password reset request.
     *
     * @param email
     * @param otp
     * @param password
     *
     * @throws ValidationException
     */
    public function confirmPassword(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'otp' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string',
        ]);

        if ($validator->fails()) {
            Log::error('Confirm password validation failed', [$request->all()]);

            return ApiResponse::error($validator->errors(), 'Password reset failed, validation error', 422);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            Log::error('confirm password user not found', [$request->all()]);

            return ApiResponse::error(null, 'Password reset failed, check your email.', 401);
        }

        // Validate OTP code
        $tokenVerified = $this->otpTokenService->verifyToken($user, $request->otp, OtpTokenType::FORGET_PASSWORD->value);
        if (! $tokenVerified) {
            Log::info('Confirm password otp token verification failed: ', [$request->all()]);

            return ApiResponse::error(null, 'OTP verification failed, invalid OTP.', 401);
        }

        // Update user password
        $user->update(['password' => Hash::make($request->password)]);

        Log::info('Confirm password for user successful: ', [$request->all()]);

        return ApiResponse::success([], 'Password setup successful');
    }

    /**
     * Verify account
     *
     * @param email
     * @param otp
     */
    public function verifyAccount(Request $request)
    {
        // Validation
        $data = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'min:6', 'max:6'],
        ]);

        // Get user by email
        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            Log::error('Verify account user not found: ', [$request->all()]);

            return ApiResponse::error([], 'Two factor authentication failed, account not activated', 401);
        }

        // Email already verified
        if ($user->hasVerifiedEmail()) {
            Log::info('Verify account user has already verify email: ', [$request->all()]);

            return ApiResponse::success([], 'Email already verified');
        }

        // Validate OTP Code
        $tokenVerified = $this->otpTokenService->verifyToken($user, $data['otp'], OtpTokenType::ACCOUNT_VERIFICATION->value);
        if (! $tokenVerified) {
            Log::info('Verify account otp token verification failed: ', [$request->all()]);

            return ApiResponse::error([], 'Email verification failed, invalid OTP, request for another OTP', 401);
        }

        // User email verified
        if ($user->markEmailAsVerified()) {
            // event(new Verified($request->user()));
            event(new Verified($user));

            // Send welcome mail for company users
            $mailService = new MailService;
            Log::info('Auth controller - Send welcome mail to new user', [
                'user_id' => $user?->id,
                'user_email' => $user?->email,
            ]);
            $mailService->sendCompanyWelcomeMail($user);
        }

        // Generate Login Token
        $token = $this->generateAuthToken($user, 24);
        Log::info('Verify account successful: ', [$request->all()]);

        return ApiResponse::success(['token' => $token, 'user' => $user], 'Email verification successful.');
    }

    /**
     * Send a new email verification token.
     */
    public function resendVerificationToken(Request $request)
    {
        // Validation
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Get user by email
        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            Log::error('Resend verification token user not found: ', [$request->all()]);

            return ApiResponse::error([], 'Two factor authentication failed, account not activated', 401);
        }

        if ($user->hasVerifiedEmail()) {
            // 208, 409, 422
            return ApiResponse::error([], 'Email already verified', 409);
        }

        // Send OTP to user email
        $this->otpTokenService->sendToken($user, OtpTokenType::ACCOUNT_VERIFICATION->value);

        return ApiResponse::success([], 'Otp verification sent to your email');
    }

    /**
     * Destroy current users token.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        Log::info('User logout: ', [$request->user()]);

        return ApiResponse::success([], 'Logout successfully');
    }

    /**
     * Destroy the user's token.
     */
    public function logoutDevices(Request $request)
    {
        // $user->tokens()->delete();
        $request->user()->tokens()->delete();
        Log::info('User logout devices: ', [$request->user()]);

        return ApiResponse::success([], 'Logout devices successfully');
    }

    /**
     * Verify login two factor authentication code (token).
     *
     * @param email
     * @param otp
     */
    public function verifyAccountTwoFactorToken(Request $request)
    {
        // Validation
        $data = $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'min:6', 'max:6'],
        ]);

        // Get user by email
        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            Log::error('Verify account 2FA failed, user not found: ', [$request->all()]);

            return ApiResponse::error([], 'Two factor authentication failed, account not activated', 401);
        }

        // check if 2fa is enabled for the user
        if ($user?->two_factor_auth != 'enable') {
            Log::error('Verify account 2FA failed, 2FA is not enabled for this account: ', [$request->all()]);

            return ApiResponse::error([], 'Two factor authentication failed, 2FA is not enabled for this account', 403);
        }

        // Validate OTP Code
        $tokenVerified = $this->otpTokenService->verifyToken($user, $data['otp'], OtpTokenType::TWO_FACTOR_AUTHENTICATION->value);
        if (! $tokenVerified) {
            Log::info('Verify account 2FA failed, otp token verification failed: ', [$request->all()]);

            return ApiResponse::error(null, 'Two factor authentication failed, invalid OTP, request for another OTP.', 401);
        }

        // $token = $user->createToken('auth_token')->plainTextToken;
        $token = $this->generateAuthToken($user, 48);

        Log::info('Verify account 2FA successful: ', [$request->all()]);

        return ApiResponse::success(['token' => $token, 'user' => $user], 'OTP verified, Login successful.');
    }

    /**
     * Resend two factor authentication code (token).
     *
     * @param email
     * @return Response
     */
    public function resendTwoFactorToken(Request $request)
    {
        // Validation
        $data = $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Get user by email
        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            Log::error('Resend 2FA token failed, user not found: ', [$request->all()]);

            return ApiResponse::error(null, 'Two factor authentication failed, account not activated.', 401);
        }

        // Check if 2fa is enabled for the user
        if ($user?->two_factor_auth != 'enable') {
            Log::error('Resend 2FA token failed, 2FA is not enabled for this account: ', [$request->all()]);

            return ApiResponse::error(null, 'Two factor authentication failed, 2FA is not enabled for this account.', 401);
        }

        // Send OTP to user email
        $this->otpTokenService->sendToken($user, OtpTokenType::TWO_FACTOR_AUTHENTICATION->value);
        Log::info('Resend 2FA token successful: ', [$request->all()]);

        return ApiResponse::success([], 'Otp verification sent to your email');
    }

    /**
     * Check current users token is valid.
     */
    public function authenticated(Request $request)
    {
        $user = $request->user();
        Log::info('Authenticated - User is authenticated: ', [
            'user_id' => $user?->id,
        ]);

        return ApiResponse::success(['user' => $user], 'successfully');
    }
}
