<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\MessageController;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/messages/send', [MessageController::class, 'sendMessage']);
    Route::get('/messages/{userId}', [MessageController::class, 'fetchMessages']);
    Route::get('/inbox', [MessageController::class, 'inbox']);
});
