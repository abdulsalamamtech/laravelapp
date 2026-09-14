<?php

namespace App\Http\Controllers\V1;

use App\Helpers\ApiResponse;
use App\Http\Controllers\Controller;
use App\Services\EmailChangeService;
use Illuminate\Http\Request;

class EmailChangeController extends Controller
{
    public function __construct(public EmailChangeService $emailChangeService) {}

    /**
     * Start an email change and send codes to both inboxes.
     */
    public function initiate(Request $request)
    {
        $data = $request->validate([
            'pending_email' => ['required', 'email', 'max:255'],
        ]);

        $user = $request->user();
        $pending = $this->emailChangeService->initiate(
            $user,
            $data['pending_email'],
            $request->ip(),
            $request->userAgent(),
        );

        return ApiResponse::success(
            ['pending_email' => $pending->pending_email],
            'We emailed a code to your current address and your new address. Complete both confirmations to schedule the change.',
        );
    }

    /**
     * Approve from the current (old) inbox.
     */
    public function confirmOld(Request $request)
    {
        $data = $request->validate([
            'pending_email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ]);

        $pending = $this->emailChangeService->confirmOld(
            $request->user(),
            $data['pending_email'],
            $data['otp'],
        );

        return ApiResponse::success(['status' => $pending->status->value], 'Current inbox approved.');
    }

    /**
     * Verify the new inbox and schedule the change.
     */
    public function confirmNew(Request $request)
    {
        $data = $request->validate([
            'pending_email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ]);

        $pending = $this->emailChangeService->confirmNew(
            $request->user(),
            $data['pending_email'],
            $data['otp'],
            $request->user()->currentAccessToken()?->id,
        );

        return ApiResponse::success(
            ['status' => $pending->status->value, 'effective_at' => $pending->effective_at?->toISOString()],
            'New inbox verified. The change will take effect after the scheduled window closes.',
        );
    }

    /**
     * Cancel a pending/scheduled change from the current inbox.
     */
    public function cancel(Request $request)
    {
        $data = $request->validate([
            'pending_email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ]);

        $pending = $this->emailChangeService->cancel(
            $request->user(),
            $data['pending_email'],
            $data['otp'],
        );

        return ApiResponse::success(['status' => $pending->status->value], 'Email change cancelled. Your existing email remains.');
    }

    /**
     * The new inbox accepts the address before the window closes.
     */
    public function accept(Request $request)
    {
        $data = $request->validate([
            'pending_email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ]);

        $pending = $this->emailChangeService->accept(
            $data['pending_email'],
            $data['otp'],
        );

        return ApiResponse::success(['status' => $pending->status->value], 'You accepted the new email. The change will apply once the window closes.');
    }

    /**
     * Report a suspicious change and retain the existing email.
     */
    public function reportSuspicious(Request $request)
    {
        $data = $request->validate([
            'pending_email' => ['required', 'email'],
            'otp' => ['required', 'string'],
        ]);

        $pending = $this->emailChangeService->reportSuspicious(
            $data['pending_email'],
            $data['otp'],
        );

        return ApiResponse::success(['status' => $pending->status->value], 'Suspicious activity reported. Your existing email was retained and all sessions were signed out.');
    }
}
