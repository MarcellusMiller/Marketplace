<?php

use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\LogoutController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post("register", [RegisterController::class, "store"]);
Route::post("login", [LoginController::class, "store"]);

Route::middleware("auth:sanctum")->group(function () {
    Route::post("logout", [LogoutController::class, "destroy"]);
    // Route::get("/me", [UserController::class, "show"]);
});