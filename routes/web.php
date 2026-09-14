<?php

use App\Http\Controllers\MailPreviewController;
use App\Http\Controllers\SocialiteController;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', fn (): Factory|\Illuminate\Contracts\View\View => view('welcome'));

Route::get('/login', function (Request $request) {
    if ($request->expectsJson()) {
        return response()->json(['message' => 'Unauthenticated.'], 401);
    }

    return redirect('/administrator/login');
})->name('login');

// OAuth (Socialite) - Google, GitHub, extensible for more
Route::get('/auth/{provider}', [SocialiteController::class, 'redirect'])
    ->whereIn('provider', ['google', 'github'])
    ->name('auth.socialite.redirect');

Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->whereIn('provider', ['google', 'github'])
    ->name('auth.socialite.callback');

// Mail preview (local/staging/testing only or enabled via config)
if (config('app.mail_preview_enabled', false) || in_array(app()->environment(), ['local', 'staging', 'testing'])) {
    Route::get('/mail', [MailPreviewController::class, 'index'])->name('mail.index');
    Route::get('/mail/{slug}', [MailPreviewController::class, 'show'])->name('mail.show');
}
