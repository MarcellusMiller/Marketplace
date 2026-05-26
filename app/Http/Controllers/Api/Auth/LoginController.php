<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Auth\LoginRequest;
use App\Actions\Auth\LoginUserAction;

class LoginController extends Controller
{
    public function store(LoginRequest $request)
    {
        $result = (new LoginUserAction())->execute($request->validated());

        return response()->json([
            "message" => "User logged in successfully.",
            "user" => $result["user"],
            "token" => $result["token"],
        ]);
    }
}
