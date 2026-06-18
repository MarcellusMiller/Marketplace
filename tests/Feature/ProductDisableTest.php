<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RoleSeeder::class);
});

it("allows sellers to disable their own products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var Product $product */
    $product = Product::factory()
        ->for($seller, "seller")
        ->create([
            "status" => ProductStatus::Active,
        ]);

    actingAs($seller);

    $response = deleteJson("/api/products/{$product->id}");

    $response
        ->assertOk()
        ->assertJsonPath("data.id", $product->id)
        ->assertJsonPath("data.status", "disabled");

    assertDatabaseHas("products", [
        "id" => $product->id,
        "seller_id" => $seller->id,
        "status" => "disabled",
    ]);
});

it("prevents sellers from disabling another seller products", function () {
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

    $response = deleteJson("/api/products/{$product->id}");

    $response->assertForbidden();
});

it("allows admins to disable any product", function () {
    /** @var User $admin */
    $admin = User::factory()->create();
    $admin->assignRole("admin");

    /** @var Product $product */
    $product = Product::factory()->create([
        "status" => ProductStatus::Active,
    ]);

    actingAs($admin);

    $response = deleteJson("/api/products/{$product->id}");

    $response
        ->assertOk()
        ->assertJsonPath("data.id", $product->id)
        ->assertJsonPath("data.status", "disabled");
});

it("prevents buyers from disabling products", function () {
    /** @var User $buyer */
    $buyer = User::factory()->create();
    $buyer->assignRole("buyer");

    /** @var Product $product */
    $product = Product::factory()->create();

    actingAs($buyer);

    $response = deleteJson("/api/products/{$product->id}");

    $response->assertForbidden();
});

it("prevents guests from disabling products", function () {
    /** @var Product $product */
    $product = Product::factory()->create();

    $response = deleteJson("/api/products/{$product->id}");

    $response->assertUnauthorized();
});
