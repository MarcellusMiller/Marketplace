<?php

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it("allows sellers to update their own products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $category = Category::factory()->create();

    $product = Product::factory()
        ->for($seller, "seller")
        ->create();

    $response = $this
        ->actingAs($seller)
        ->patchJson("/api/products/{$product->id}", [
            "category_id" => $category->id,
            "name" => "Updated Product",
            "description" => "Updated description.",
            "price_cents" => 3990,
            "stock" => 7,
            "status" => ProductStatus::Inactive->value,
        ]);

    $response
        ->assertOk()
        ->assertJsonPath("data.id", $product->id)
        ->assertJsonPath("data.name", "Updated Product")
        ->assertJsonPath("data.slug", "updated-product")
        ->assertJsonPath("data.price_cents", 3990)
        ->assertJsonPath("data.stock", 7)
        ->assertJsonPath("data.status", "inactive")
        ->assertJsonPath("data.category.id", $category->id);

    $this->assertDatabaseHas("products", [
        "id" => $product->id,
        "seller_id" => $seller->id,
        "category_id" => $category->id,
        "name" => "Updated Product",
        "slug" => "updated-product",
        "price_cents" => 3990,
        "stock" => 7,
        "status" => "inactive",
    ]);
});

it("allows sellers to partially update their own products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $product = Product::factory()
        ->for($seller, "seller")
        ->create([
            "name" => "Original Product",
            "slug" => "original-product",
            "price_cents" => 1000,
        ]);

    $response = $this
        ->actingAs($seller)
        ->patchJson("/api/products/{$product->id}", [
            "price_cents" => 1500,
        ]);

    $response
        ->assertOk()
        ->assertJsonPath("data.name", "Original Product")
        ->assertJsonPath("data.slug", "original-product")
        ->assertJsonPath("data.price_cents", 1500);

    $this->assertDatabaseHas("products", [
        "id" => $product->id,
        "name" => "Original Product",
        "slug" => "original-product",
        "price_cents" => 1500,
    ]);
});

it("prevents sellers from updating another seller products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $anotherSeller = User::factory()->create();
    $anotherSeller->assignRole("seller");

    $product = Product::factory()
        ->for($anotherSeller, "seller")
        ->create();

    $response = $this
        ->actingAs($seller)
        ->patchJson("/api/products/{$product->id}", [
            "name" => "Forbidden Update",
        ]);

    $response->assertForbidden();
});

it("allows admins to update any product", function () {
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    $product = Product::factory()->create();

    $response = $this
        ->actingAs($admin)
        ->patchJson("/api/products/{$product->id}", [
            "name" => "Admin Updated Product",
        ]);

    $response
        ->assertOk()
        ->assertJsonPath("data.name", "Admin Updated Product")
        ->assertJsonPath("data.slug", "admin-updated-product");
});

it("prevents buyers from updating products", function () {
    $buyer = User::factory()->create();
    $buyer->assignRole("buyer");

    $product = Product::factory()->create();

    $response = $this
        ->actingAs($buyer)
        ->patchJson("/api/products/{$product->id}", [
            "name" => "Buyer Update",
        ]);

    $response->assertForbidden();
});

it("prevents guests from updating products", function () {
    $product = Product::factory()->create();

    $response = $this->patchJson("/api/products/{$product->id}", [
        "name" => "Guest Update",
    ]);

    $response->assertUnauthorized();
});

it("validates update product fields", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $product = Product::factory()
        ->for($seller, "seller")
        ->create();

    $response = $this
        ->actingAs($seller)
        ->patchJson("/api/products/{$product->id}", [
            "price_cents" => 0,
            "stock" => -1,
            "status" => "invalid-status",
        ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            "price_cents",
            "stock",
            "status",
        ]);
});