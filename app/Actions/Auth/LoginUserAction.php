<?php

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginUserAction
{
    public function execute(array $credentials): array
    {
        $user = User::where("email", $credentials["email"])->first();

        if(!$user || !Hash::check($credentials["password"], $user->password)) {
            throw new \Exception("Invalid credentials.");
        }
        $token = $user->createToken("auth_token")->plainTextToken;
        return [
            "user" => $user,
            "token" => $token,
        ];        
    }
}
