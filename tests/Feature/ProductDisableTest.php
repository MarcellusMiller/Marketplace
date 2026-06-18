<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it("allows sellers to disable their own products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $product = Product::factory()
        ->for($seller, "seller")
        ->create([
            "status" => ProductStatus::Active,
        ]);

    $response = $this
        ->actingAs($seller)
        ->deleteJson("/api/products/{$product->id}");

    $response
        ->assertOk()
        ->assertJsonPath("data.id", $product->id)
        ->assertJsonPath("data.status", "disabled");

    $this->assertDatabaseHas("products", [
        "id" => $product->id,
        "seller_id" => $seller->id,
        "status" => "disabled",
    ]);
});

it("prevents sellers from disabling another seller products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $anotherSeller = User::factory()->create();
    $anotherSeller->assignRole("seller");

    $product = Product::factory()
        ->for($anotherSeller, "seller")
        ->create();

    $response = $this
        ->actingAs($seller)
        ->deleteJson("/api/products/{$product->id}");

    $response->assertForbidden();
});

it("allows admins to disable any product", function () {
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    $product = Product::factory()->create([
        "status" => ProductStatus::Active,
    ]);

    $response = $this
        ->actingAs($admin)
        ->deleteJson("/api/products/{$product->id}");

    $response
        ->assertOk()
        ->assertJsonPath("data.id", $product->id)
        ->assertJsonPath("data.status", "disabled");
});

it("prevents buyers from disabling products", function () {
    $buyer = User::factory()->create();
    $buyer->assignRole("buyer");

    $product = Product::factory()->create();

    $response = $this
        ->actingAs($buyer)
        ->deleteJson("/api/products/{$product->id}");

    $response->assertForbidden();
});

it("prevents guests from disabling products", function () {
    $product = Product::factory()->create();

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertUnauthorized();
});