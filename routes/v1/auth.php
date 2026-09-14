<?php

use App\Http\Controllers\V1\AuthController;
use App\Http\Controllers\V1\EmailChangeController;
use Illuminate\Support\Facades\Route;

// Test Login
Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:6,1');

// Register
Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:10,1');

// Public routes with rate limiting
Route::middleware(['throttle:6,1'])->group(function () {
    // verify forget password email, otp && Enter new password
    Route::post('/confirm-password', [AuthController::class, 'confirmPassword']);
    // Forget password [email] = send 6 digit otp
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    // Verify account
    Route::post('/verify-account', [AuthController::class, 'verifyAccount']);
    // Verify two factor auth
    Route::post('/verify-two-factor-otp', [AuthController::class, 'verifyAccountTwoFactorToken']);
    // Resend two factor auth otp
    Route::post('/resend-two-factor-otp', [AuthController::class, 'resendTwoFactorToken']);
    /**
     * Resend verification code
     */
    Route::post('/resend-token', [AuthController::class, 'resendVerificationToken']);

    /**
     * Search for a pending email change and report suspicious activity,
     * retaining the existing email. Uses the code sent to the current inbox.
     */
    Route::post('/email/change/report', [EmailChangeController::class, 'reportSuspicious']);
    /**
     * New inbox accepts the scheduled change before the window closes.
     */
    Route::post('/email/change/accept', [EmailChangeController::class, 'accept']);
});

// Protected routes
Route::middleware(['auth:sanctum'])->group(function () {
    /**
     * Reset password [old & new password] - need more edge test
     */
    Route::post('/reset-password', [AuthController::class, 'updatePassword']);

    /**
     * Destroy current user's token.
     *
     * @param token
     * @return Response
     */
    Route::post('/logout', [AuthController::class, 'logout'])
        ->name('api.logout');

    /**
     * Destroy the user's token.
     *
     * @param token
     * @return Response
     */
    Route::post('/logout-devices', [AuthController::class, 'logoutDevices'])
        ->name('api.logout-devices');

    /**
     * Is user authenticated
     */
    Route::get('/authenticated', [AuthController::class, 'authenticated']);

    /**
     * Start an email change (codes emailed to both inboxes)
     */
    Route::post('/email/change', [EmailChangeController::class, 'initiate'])->middleware('throttle:6,1');
    /**
     * Approve the change from the current inbox
     */
    Route::post('/email/change/confirm-old', [EmailChangeController::class, 'confirmOld']);
    /**
     * Verify the new inbox and schedule the change
     */
    Route::post('/email/change/confirm-new', [EmailChangeController::class, 'confirmNew']);
    /**
     * Cancel a pending/scheduled change using the current-inbox code
     */
    Route::post('/email/change/cancel', [EmailChangeController::class, 'cancel']);
});
