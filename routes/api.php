<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenController;
use App\Http\Controllers\UserController;


Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [UserController::class, 'show']);
    Route::delete('/user', [AuthController::class, 'deleteAccount']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
});

Route::get('/validate-token', [TokenController::class, 'validateToken']);

Route::get('/data', function () {
    return response()->json([
        'message' => 'Hello from Microservice 1',
        'timestamp' => now()->toDateTimeString(),
    ]);
});
