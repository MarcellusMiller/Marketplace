<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patchJson;

uses(RefreshDatabase::class);

it("allows users to update quantities from their own cart items", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        "user_id" => $user->id,
    ]);

    /** @var Product $product */
    $product = Product::factory()->create([
        "stock" => 10,
        "price_cents" => 2500,
    ]);

    /** @var CartItem $cartItem */
    $cartItem = CartItem::factory()->create([
        "cart_id" => $cart->id,
        "product_id" => $product->id,
        "quantity" => 2,
        "unit_price_cents" => 2500,
    ]);

    actingAs($user);

    $response = patchJson("/api/cart/items/{$cartItem->id}", [
        "quantity" => 5,
    ]);

    $response
        ->assertOk()
        ->assertJsonPath("data.id", $cart->id)
        ->assertJsonPath("data.subtotal_cents", 12500)
        ->assertJsonPath("data.items_count", 5)
        ->assertJsonPath("data.items.0.id", $cartItem->id)
        ->assertJsonPath("data.items.0.quantity", 5)
        ->assertJsonPath("data.items.0.line_total_cents", 12500);

    assertDatabaseHas("cart_items", [
        "id" => $cartItem->id,
        "quantity" => 5,
    ]);
});

it("prevents users from updating another user cart items", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var User $anotherUser */
    $anotherUser = User::factory()->create();

    /** @var Cart $anotherCart */
    $anotherCart = Cart::factory()->create([
        "user_id" => $anotherUser->id,
    ]);

    /** @var CartItem $cartItem */
    $cartItem = CartItem::factory()->create([
        "cart_id" => $anotherCart->id,
    ]);

    actingAs($user);

    $response = patchJson("/api/cart/items/{$cartItem->id}", [
        "quantity" => 3,
    ]);

    $response->assertForbidden();
});

it("prevents guests from updating cart items", function () {
    /** @var CartItem $cartItem */
    $cartItem = CartItem::factory()->create();

    $response = patchJson("/api/cart/items/{$cartItem->id}", [
        "quantity" => 3,
    ]);

    $response->assertUnauthorized();
});

it("prevents updating quantity above product stock", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        "user_id" => $user->id,
    ]);

    /** @var Product $product */
    $product = Product::factory()->create([
        "stock" => 2,
    ]);

    /** @var CartItem $cartItem */
    $cartItem = CartItem::factory()->create([
        "cart_id" => $cart->id,
        "product_id" => $product->id,
        "quantity" => 1,
    ]);

    actingAs($user);

    $response = patchJson("/api/cart/items/{$cartItem->id}", [
        "quantity" => 3,
    ]);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(["quantity"]);
});

it("validates required quantity", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        "user_id" => $user->id,
    ]);

    /** @var CartItem $cartItem */
    $cartItem = CartItem::factory()->create([
        "cart_id" => $cart->id,
    ]);

    actingAs($user);

    $response = patchJson("/api/cart/items/{$cartItem->id}", []);

    $response
        ->assertUnprocessable()
        ->assertJsonValidationErrors(["quantity"]);
});

it("does not decrease product stock when updating cart item quantity", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        "user_id" => $user->id,
    ]);

    /** @var Product $product */
    $product = Product::factory()->create([
        "stock" => 10,
    ]);

    /** @var CartItem $cartItem */
    $cartItem = CartItem::factory()->create([
        "cart_id" => $cart->id,
        "product_id" => $product->id,
        "quantity" => 2,
    ]);

    actingAs($user);

    patchJson("/api/cart/items/{$cartItem->id}", [
        "quantity" => 5,
    ])->assertOk();

    expect($product->refresh()->stock)->toBe(10);
});
