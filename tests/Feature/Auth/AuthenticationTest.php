<?php

use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

BeforeEach(function () {
    $this->seed(RoleSeeder::class);
});

// Test for user registration
it("register as new User", function () {
    $response = $this->postJson("/api/register", [
        "name" => "Marcellus test",
        "email" => "marcellus@test.com",
        "password" => "password123",
        "password_confirmation" => "password123"
    ]);

    $response 
        ->assertCreated()
        ->assertJsonStructure([
            "message",
            "user" => [
                "id",
                "name",
                "email",
            ],
            "token",
        ]);
});

// Test to ensure that the buyer role is assigned upon registration
it("assings buyer role when registering", function () {
    $response = $this->postJson("/api/register", [
        "name" => "Marcellus test",
        "email" => "buyer@example.com",
        "password" => "password123",
        "password_confirmation" => "password123"
    ]);
    $response->assertCreated();
    $user = User::where("email", "buyer@example.com")->first();

    expect($user)->not->toBeNull();
    expect($user->hasRole("buyer"))->toBeTrue();
});

it("logs in an existing user", function () {
    $user = User::factory()->create([
        "email" => "login@example.com",
        "password" => "password123"
    ]);

    $user->assignRole("buyer");

    $response  = $this->postJson("/api/login", [
        "email" => "login@example.com",
        "password" => "password123",
    ]);

    $response 
        ->assertOk()
        ->assertJsonStructure([
            "message",
            "user",
            "token",
        ]);
});

it("rejects login with invalid credentials", function () {
    User::factory()->create([
        "email" => "wrong-password@example.com",
        "password" => "password123"
    ]);

    $response = $this->postJson("/api/login", [
        "email" => "wrong-password@example.com",
        "password" => "wrongpassword"
    ]);

    $response->assertUnauthorized()
        ->assertJson([
            "message" => "Invalid email or password.",
        ]);
});

it("returns the authenticated user", function () {
    $user = User::factory()->create([
        "email" => "me@example.com",
    ]);

    $user->assignRole("buyer");
    $token = $user->createToken("auth_token")->plainTextToken;

    $response = $this
        ->withHeaders([
            "Authorization" => "Bearer {$token}",
        ])
        ->getJson("/api/me");
    $response 
        ->assertOk()
        ->assertJson([
            "user" => [
                "id" => $user->id,
                "name" => $user->name,
                "email" => $user->email,
                "avatar_url" => $user->avatar_url,
            ],
            "roles" => ["buyer"], 
            ]);
});

it("does not return authenticated user without token", function () {
    $response = $this->getJson("/api/me");

    $response->assertUnauthorized();
});

it("logs out the authenticated user", function () {
    $user = User::factory()->create([
        "email" => "logout@example.com"
    ]);

    $user->assignRole("buyer");
    $token = $user->createToken("auth_token")->plainTextToken;

    $response = $this
        ->withHeaders([
            "Authorization" => "Bearer {$token}",
        ])
        ->postJson("/api/logout");

    $response
        ->assertOk()
        ->assertJson([
            "message" => "Logged out successfully",
        ]);

    $this->assertDatabaseMissing("personal_access_tokens", [
        "tokenable_id" => $user->id,

    ]);

});