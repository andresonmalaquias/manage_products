<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;


Route::post('login', [AuthController::class, 'login'])->name('login');

Route::middleware('auth:api')->group(function () {
    Route::apiResource('category', CategoryController::class);
    Route::apiResource('product', ProductController::class);
});
