<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageProcessingController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\ApiKeyController;

// Public authentication routes - need web middleware for CSRF and sessions
Route::middleware(['web'])->prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/check', [AuthController::class, 'check']);
});

// Protected authentication routes (using custom API auth middleware)
Route::middleware(['web', \App\Http\Middleware\ApiAuthenticate::class])->prefix('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
});

// Protected API key management routes (using custom API auth middleware)
Route::middleware(['web', \App\Http\Middleware\ApiAuthenticate::class])->prefix('api-keys')->group(function () {
    Route::post('/', [ApiKeyController::class, 'store']);
    Route::get('/status', [ApiKeyController::class, 'status']);
    Route::post('/validate', [ApiKeyController::class, 'validate']);
    Route::delete('/', [ApiKeyController::class, 'destroy']);
    
    // Individual API key management
    Route::post('/openai', [ApiKeyController::class, 'storeOpenAI']);
    Route::post('/elevenlabs', [ApiKeyController::class, 'storeElevenLabs']);
    Route::post('/openai/test', [ApiKeyController::class, 'testOpenAI']);
    Route::post('/elevenlabs/test', [ApiKeyController::class, 'testElevenLabs']);
});

// Protected image processing routes (backend proxy)
Route::middleware(['web', \App\Http\Middleware\ApiAuthenticate::class])->group(function () {
    Route::post('/process-image-proxy', [ImageProcessingController::class, 'processImageProxy']);
});

// Legacy routes (for backward compatibility)
Route::post('/process-image', [ImageProcessingController::class, 'processImage']);
Route::post('/process-image-server', [ImageProcessingController::class, 'processImageWithServerKeys']);
Route::post('/validate-api-keys', [ImageProcessingController::class, 'validateApiKeys']);

// User route for Sanctum compatibility
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum'); 