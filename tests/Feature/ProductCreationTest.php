<?php

use App\Enums\ProductStatus;
use App\Models\Category;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;
use function Pest\Laravel\seed;

uses(RefreshDatabase::class);

beforeEach(function () {
    seed(RoleSeeder::class);
});

it("allows sellers to create products", function () {
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    /** @var Category $category */
    $category = Category::factory()->create();

    actingAs($seller);

    $response = postJson("/api/products", [
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

    assertDatabaseHas("products", [
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
    /** @var User $buyer */
    $buyer = User::factory()->create();
    $buyer->assignRole("buyer");

    /** @var Category $category */
    $category = Category::factory()->create();

    actingAs($buyer);

    $response = postJson("/api/products", [
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
    /** @var Category $category */
    $category = Category::factory()->create();

    $response = postJson("/api/products", [
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
    /** @var User $seller */
    $seller = User::factory()->create();
    $seller->assignRole("seller");

    actingAs($seller);

    $response = postJson("/api/products", []);

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
