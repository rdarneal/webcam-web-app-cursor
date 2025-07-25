<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Named login route (required by Laravel auth middleware)
Route::get('/login', function () {
    return view('welcome');
})->name('login');

// Catch all other routes and return the main app (for SPA routing)
Route::get('/{any}', function () {
    return view('welcome');
})->where('any', '.*');
