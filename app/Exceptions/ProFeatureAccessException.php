<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProFeatureAccessException extends Exception
{
    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request)
    {
        Log::alert('Premium feature exception - this user is trying to access premium feature', [
            'user' => $request?->user(),
            'ip' => $request?->ip(),
            'request' => $request?->all(),
        ]);

        // If it's an API request, return a JSON error
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'This feature is exclusively for higher plan, You need to upgrade your account to access this feature.',
            ], 403);
        }

        // For web requests, redirect back with an error message
        return redirect()->back()->with('error', 'You need to upgrade your account to access this feature.');
    }
}
