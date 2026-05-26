<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Models\User;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
       $body = $request->validated();
        // Model ja usa cast para criptografar a senha
       $user = User::create([
            "name" => $body["name"],
            "email" => $body["email"],
            "password" => $body["password"],
       ]);
        $user->assignRole("buyer");
         $token = $user->createToken("auth_token")->plainTextToken;
        return response()->json([
            "message" => "User registered successfully.",
            "user" => $user,
            "token" => $token,
        ], 201);
       
    }
}
