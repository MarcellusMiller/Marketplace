<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::post("register", [RegisterController::class, "store"]);
Route::post("login", [LoginController::class, "store"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("logout", [LogoutController::class, "destroy"]);
    Route::get("me", [MeController::class, "show"]);
});

Route::get("products", [ProductController::class, "index"]);
Route::get("products/{product:slug}", [ProductController::class, "show"]);
Route::get("categories", [CategoryController::class, "index"]);