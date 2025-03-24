<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TokenController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/update-password', [AuthController::class, 'updatePassword']);
    Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
});

Route::get('/validate-token', [TokenController::class, 'validateToken']);
