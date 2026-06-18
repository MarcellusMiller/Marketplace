<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\MeController;
use App\Http\Controllers\Api\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\CategoryController;

Route::post("register", [RegisterController::class, "store"]);
Route::post("login", [LoginController::class, "store"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("logout", [LogoutController::class, "destroy"]);
    Route::get("me", [MeController::class, "show"]);
    Route::post("products", [ProductController::class, "store"]);
    Route::patch("products/{product}", [ProductController::class, "update"]);
    Route::delete("products/{product}", [ProductController::class, "destroy"]);

    Route::get("cart", [CartController::class, "show"]);
    Route::post("cart/items", [CartController::class, "addItem"]);
    Route::patch("cart/items/{cartItem}", [CartController::class, "updateItem"]);
    Route::delete("cart/items/{cartItem}", [CartController::class, "removeItem"]);
});

Route::get("products", [ProductController::class, "index"]);
Route::get("products/{product:slug}", [ProductController::class, "show"]);
Route::get("categories", [CategoryController::class, "index"]);
