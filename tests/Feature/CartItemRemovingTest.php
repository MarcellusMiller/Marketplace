<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\deleteJson;

uses(RefreshDatabase::class);

it("allows users to remove items from their own cart", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        "user_id" => $user->id,
    ]);

    /** @var Product $product */
    $product = Product::factory()->create([
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

    $response = deleteJson("/api/cart/items/{$cartItem->id}");

    $response
        ->assertOk()
        ->assertJsonPath("data.id", $cart->id)
        ->assertJsonPath("data.subtotal_cents", 0)
        ->assertJsonPath("data.items_count", 0)
        ->assertJsonCount(0, "data.items");

    assertDatabaseMissing("cart_items", [
        "id" => $cartItem->id,
    ]);
});

it("prevents users from removing another user cart items", function () {
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

    $response = deleteJson("/api/cart/items/{$cartItem->id}");

    $response->assertForbidden();
});

it("prevents guests from removing cart items", function () {
    /** @var CartItem $cartItem */
    $cartItem = CartItem::factory()->create();

    $response = deleteJson("/api/cart/items/{$cartItem->id}");

    $response->assertUnauthorized();
});
