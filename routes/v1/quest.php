<?php

use App\Http\Controllers\V1\Quest\ChatMessageController;
use App\Http\Controllers\V1\Quest\ContactController;
use App\Http\Controllers\V1\Quest\WaitlistController;
use Illuminate\Support\Facades\Route;

// Public routes with rate limiting
Route::middleware(['throttle:6,1'])->group(function () {
    // Contacts
    Route::post('contacts', [ContactController::class, 'store']);

    // Waitlists
    Route::post('waitlists', [WaitlistController::class, 'store']);

    // Chat messages
    Route::post('chat-messages', [ChatMessageController::class, 'store']);
});
