<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleAuthController;

Route::get('/', function () {
    return view('welcome');
});

// Named login route (required by Laravel auth middleware)
Route::get('/login', function () {
    return view('welcome');
})->name('login');

// Google OAuth callback for web (popup)
Route::get('/auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);

// Catch all other routes and return the main app (for SPA routing)
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
