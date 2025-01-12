<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/users', [UserController::class, 'register']);
Route::post('/users/login', [UserController::class, 'login']);

Route::middleware('token')->group(function () {
    Route::get('/users/profile', [UserController::class, 'profile']);
    Route::patch('/users/profile', [UserController::class, 'update']);
    Route::delete('/users/logout', [UserController::class, 'logout']);

    Route::post('/categories', [CategoryController::class, 'create']);
    Route::get('/categories/{id}', [CategoryController::class, 'detail'])->where('id', '[0-9]+');
    Route::put('/categories/{id}', [CategoryController::class, 'update'])->where('id', '[0-9]+');
    Route::delete('/categories/{id}', [CategoryController::class, 'delete'])->where('id', '[0-9]+');
    Route::get('/categories/list', [CategoryController::class, 'list']);
});