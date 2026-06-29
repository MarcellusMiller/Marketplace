<?php

use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

it("allows authenticated users to add active products to cart", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Product $product */
    $product = Product::factory()->create([
        "price_cents" => 2500,
        "stock" => 10,
        "status" => ProductStatus::Active,
    ]);

    actingAs($user);

    $response = postJson("/api/cart/items", [
        "product_id" => $product->id,
        "quantity" => 2,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath("data.subtotal_cents", 5000)
        ->assertJsonPath("data.items_count", 2)
        ->assertJsonPath("data.items.0.product.id", $product->id)
        ->assertJsonPath("data.items.0.quantity", 2)
        ->assertJsonPath("data.items.0.unit_price_cents", 2500)
        ->assertJsonPath("data.items.0.line_total_cents", 5000);

    assertDatabaseHas("carts", [
        "user_id" => $user->id,
    ]);

    assertDatabaseHas("cart_items", [
        "product_id" => $product->id,
        "quantity" => 2,
        "unit_price_cents" => 2500,
    ]);
});

it("increments quantity when product already exists in cart", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        "user_id" => $user->id,
    ]);

    /** @var Product $product */
    $product = Product::factory()->create([
        "price_cents" => 2500,
        "stock" => 10,
        "status" => ProductStatus::Active,
    ]);

    CartItem::factory()->create([
        "cart_id" => $cart->id,
        "product_id" => $product->id,
        "quantity" => 2,
        "unit_price_cents" => 2500,
    ]);

    actingAs($user);

    $response = postJson("/api/cart/items", [
        "product_id" => $product->id,
        "quantity" => 3,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath("data.subtotal_cents", 12500)
        ->assertJsonPath("data.items_count", 5)
        ->assertJsonPath("data.items.0.quantity", 5)
        ->assertJsonPath("data.items.0.line_total_cents", 12500);

    assertDatabaseHas("cart_items", [
        "cart_id" => $cart->id,
        "product_id" => $product->id,
        "quantity" => 5,
        "unit_price_cents" => 2500,
    ]);
});

it("prevents guests from adding products to cart", function () {
    /** @var Product $product */
    $product = Product::factory()->create([
        "status" => ProductStatus::Active,
    ]);

    $response = postJson("/api/cart/items", [
        "product_id" => $product->id,
        "quantity" => 1,
    ]);

    $response->assertUnauthorized();
});

it("prevents adding inactive products to cart", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Product $product */
    $product = Product::factory()->create([
        "status" => ProductStatus::Inactive,
    ]);

    actingAs($user);

    $response = postJson("/api/cart/items", [
        "product_id" => $product->id,
        "quantity" => 1,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(["product_id"]);
});

it("prevents adding more than available stock", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Product $product */
    $product = Product::factory()->create([
        "stock" => 2,
        "status" => ProductStatus::Active,
    ]);

    actingAs($user);

    $response = postJson("/api/cart/items", [
        "product_id" => $product->id,
        "quantity" => 3,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(["quantity"]);
});

it("validates required fields", function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    $response = postJson("/api/cart/items", []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors([
            "product_id",
            "quantity",
        ]);
});

it("prevents adding quantity when cart total would exceed product stock", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        "user_id" => $user->id,
    ]);

    /** @var Product $product */
    $product = Product::factory()->create([
        "stock" => 5,
        "status" => ProductStatus::Active,
    ]);

    CartItem::factory()->create([
        "cart_id" => $cart->id,
        "product_id" => $product->id,
        "quantity" => 3,
        "unit_price_cents" => $product->price_cents,
    ]);

    actingAs($user);

    $response = postJson("/api/cart/items", [
        "product_id" => $product->id,
        "quantity" => 3,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(["quantity"]);
});

it("does not decrease product stock when adding products to cart", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Product $product */
    $product = Product::factory()->create([
        "stock" => 10,
        "status" => ProductStatus::Active,
    ]);

    actingAs($user);

    postJson("/api/cart/items", [
        "product_id" => $product->id,
        "quantity" => 2,
    ])->assertOk();

    expect($product->refresh()->stock)->toBe(10);
});
