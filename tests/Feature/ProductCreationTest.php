<?php

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed(RoleSeeder::class);
});

it("allows sellers to create products", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $category = Category::factory()->create();

    $response = $this
        ->actingAs($seller)
        ->postJson("/api/products", [
            "category_id" => $category->id,
            "name" => "Mechanical Keyboard",
            "description" => "A compact mechanical keyboard.",
            "price_cents" => 25990,
            "stock" => 10,
            "status" => ProductStatus::Active->value,
        ]);

    $response
        ->assertCreated()
        ->assertJsonPath("data.name", "Mechanical Keyboard")
        ->assertJsonPath("data.slug", "mechanical-keyboard")
        ->assertJsonPath("data.price_cents", 25990)
        ->assertJsonPath("data.status", "active")
        ->assertJsonPath("data.category.id", $category->id)
        ->assertJsonPath("data.seller.id", $seller->id);

    $this->assertDatabaseHas("products", [
        "seller_id" => $seller->id,
        "category_id" => $category->id,
        "name" => "Mechanical Keyboard",
        "slug" => "mechanical-keyboard",
        "price_cents" => 25990,
        "stock" => 10,
        "status" => "active",
    ]);
});

it("prevents buyers from creating products", function () {
    $buyer = User::factory()->create();
    $buyer->assignRole("buyer");

    $category = Category::factory()->create();

    $response = $this
        ->actingAs($buyer)
        ->postJson("/api/products", [
            "category_id" => $category->id,
            "name" => "Mechanical Keyboard",
            "description" => "A compact mechanical keyboard.",
            "price_cents" => 25990,
            "stock" => 10,
            "status" => ProductStatus::Active->value,
        ]);

    $response->assertForbidden();
});

it("prevents guests from creating products", function () {
    $category = Category::factory()->create();

    $response = $this->postJson("/api/products", [
        "category_id" => $category->id,
        "name" => "Mechanical Keyboard",
        "description" => "A compact mechanical keyboard.",
        "price_cents" => 25990,
        "stock" => 10,
        "status" => ProductStatus::Active->value,
    ]);

    $response->assertUnauthorized();
});

it("validates required product fields", function () {
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    $response = $this
        ->actingAs($seller)
        ->postJson("/api/products", []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            "category_id",
            "name",
            "price_cents",
            "stock",
            "status",
        ]);
});