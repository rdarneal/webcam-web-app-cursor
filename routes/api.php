<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ImageProcessingController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Image processing routes
Route::post('/process-image', [ImageProcessingController::class, 'processImage']);
Route::post('/process-image-server', [ImageProcessingController::class, 'processImageWithServerKeys']);
Route::post('/validate-api-keys', [ImageProcessingController::class, 'validateApiKeys']); 