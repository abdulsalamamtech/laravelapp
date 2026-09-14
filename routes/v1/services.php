<?php

use App\Notifications\AlertUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Admin
Route::prefix('services')->group(function () {
    // Test route
    Route::get('/test', fn (): string => 'This is services route');
});

// Test notification
Route::middleware(['auth:sanctum'])->get('notify', function (Request $request): string {
    $user = $request->user();
    $validate = $request->validate([
        'title' => ['nullable', 'max:2550'],
        'message' => ['nullable'],
    ]);
    $data = [
        'title' => $validate['title'] ?? 'Testing Notification Flow',
        'message' => $validate['message'] ?? 'This is just to see if the user can receive email notification.',
    ];
    $user->notify(new AlertUser($data));

    return 'Notification Alert Sent To '.$user?->email;
});
