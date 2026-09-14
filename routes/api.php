<?php

use App\Notifications\AlertUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request) => $request->user())->middleware('auth:sanctum');

// V1 Auth Routes
Route::prefix('v1')->group(function () {
    // Authentication
    require __DIR__.'/v1/auth.php';
    // Admin
    require __DIR__.'/v1/admin.php';
    // Services
    require __DIR__.'/v1/services.php';
    // Quests
    require __DIR__.'/v1/quest.php';
});

// Test notification
Route::middleware(['auth:sanctum'])->get('/notify', function (Request $request): string {
    $user = $request->user();

    $data = [
        'title' => 'Business Metrics',
        'message' => 'Your business metrics is ready!',
    ];
    $user->notify(new AlertUser($data));

    return 'notification sent!';
});
