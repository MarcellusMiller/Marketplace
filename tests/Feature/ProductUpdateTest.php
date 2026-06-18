<?php

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patchJson;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RoleSeeder::class);
});

it("allows sellers to update their own products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var Category $category */
    $category = Category::factory()->create();

    /** @var Product $product */
    $product = Product::factory()
        ->for($seller, "seller")
        ->create();

    actingAs($seller);

    $response = patchJson("/api/products/{$product->id}", [
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

    assertDatabaseHas("products", [
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
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var Product $product */
    $product = Product::factory()
        ->for($seller, "seller")
        ->create([
            "name" => "Original Product",
            "slug" => "original-product",
            "price_cents" => 1000,
        ]);

    actingAs($seller);

    $response = patchJson("/api/products/{$product->id}", [
        "price_cents" => 1500,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath("data.name", "Original Product")
        ->assertJsonPath("data.slug", "original-product")
        ->assertJsonPath("data.price_cents", 1500);

    assertDatabaseHas("products", [
        "id" => $product->id,
        "name" => "Original Product",
        "slug" => "original-product",
        "price_cents" => 1500,
    ]);
});

it("prevents sellers from updating another seller products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var User $anotherSeller */
    $anotherSeller = User::factory()->create();
    $anotherSeller->assignRole("seller");

    /** @var Product $product */
    $product = Product::factory()
        ->for($anotherSeller, "seller")
        ->create();

    actingAs($seller);

    $response = patchJson("/api/products/{$product->id}", [
        "name" => "Forbidden Update",
    ]);

    $response->assertForbidden();
});

it("allows admins to update any product", function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    /** @var Product $product */
    $product = Product::factory()->create();

    actingAs($admin);

    $response = patchJson("/api/products/{$product->id}", [
        "name" => "Admin Updated Product",
    ]);

    $response
        ->assertOk()
        ->assertJsonPath("data.name", "Admin Updated Product")
        ->assertJsonPath("data.slug", "admin-updated-product");
});

it("prevents buyers from updating products", function () {
    /** @var User $buyer */
    $buyer = User::factory()->create();
    $buyer->assignRole("buyer");

    /** @var Product $product */
    $product = Product::factory()->create();

    actingAs($buyer);

    $response = patchJson("/api/products/{$product->id}", [
        "name" => "Buyer Update",
    ]);

    $response->assertForbidden();
});

it("prevents guests from updating products", function () {
    /** @var Product $product */
    $product = Product::factory()->create();

    $response = patchJson("/api/products/{$product->id}", [
        "name" => "Guest Update",
    ]);

    $response->assertUnauthorized();
});

it("validates update product fields", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var Product $product */
    $product = Product::factory()
        ->for($seller, "seller")
        ->create();

    actingAs($seller);

    $response = patchJson("/api/products/{$product->id}", [
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
