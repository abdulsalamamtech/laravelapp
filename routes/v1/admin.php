<?php

use App\Http\Controllers\V1\Admin\AdminContactController;
use Illuminate\Support\Facades\Route;

// Admin
Route::prefix('admin')->middleware(['auth:sanctum', 'role:admin|super_admin'])->group(function () {
    // Test route
    Route::get('/test', fn (): string => 'This is admin route');

    // Contact Messages
    Route::prefix('contacts')
        ->controller(AdminContactController::class)
        ->group(function () {
            // Plans
            Route::get('/', 'index');
            Route::put('/{contact}', 'update');
            Route::get('/{contact}', 'show');
            Route::delete('/{contact}', 'destroy');
        });
});
