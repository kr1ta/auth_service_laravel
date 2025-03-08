<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/create-task', [TaskController::class, 'createTask']);
    Route::get('/user-tasks', [TaskController::class, 'getUserTasks']);
});

use App\Http\Controllers\UserController;

Route::get('/user/{id}', [UserController::class, 'show']);  
