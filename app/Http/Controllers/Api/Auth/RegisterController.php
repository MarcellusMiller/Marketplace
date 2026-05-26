<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\RegisterRequest;
use App\Actions\Auth\RegisterUserAction;

class RegisterController extends Controller
{
    public function store(RegisterRequest $request)
    {
       $result = (new RegisterUserAction())->execute($request->validated());

        return response()->json([
            "message" => "User registered successfully.",
            "user" => $result['user'],
            "token" => $result['token'],
        ], 201);
       
    }
}
