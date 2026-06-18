<?php

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it("creates and returns an empty cart for authenticated users", function () {
    /** @var User $user */
    $user = User::factory()->create();

    actingAs($user);

    $response = getJson("/api/cart");

    $response
        ->assertOk()
        ->assertJsonPath("data.subtotal_cents", 0)
        ->assertJsonPath("data.items_count", 0)
        ->assertJsonCount(0, "data.items");

    assertDatabaseHas("carts", [
        "user_id" => $user->id,
    ]);
});

it("returns the authenticated user cart with items", function () {
    /** @var User $user */
    $user = User::factory()->create();

    /** @var Cart $cart */
    $cart = Cart::factory()->create([
        "user_id" => $user->id,
    ]);

    /** @var Product $product */
    $product = Product::factory()->create([
        "name" => "Mechanical Keyboard",
        "slug" => "mechanical-keyboard",
        "price_cents" => 2500,
        "stock" => 10,
    ]);

    ProductImage::factory()
        ->for($product)
        ->main()
        ->create([
            "url" => "https://example.com/products/keyboard.jpg",
        ]);

    CartItem::factory()->create([
        "cart_id" => $cart->id,
        "product_id" => $product->id,
        "quantity" => 2,
        "unit_price_cents" => 2500,
    ]);

    actingAs($user);

    $response = getJson("/api/cart");

    $response
        ->assertOk()
        ->assertJsonPath("data.id", $cart->id)
        ->assertJsonPath("data.subtotal_cents", 5000)
        ->assertJsonPath("data.items_count", 2)
        ->assertJsonPath("data.items.0.quantity", 2)
        ->assertJsonPath("data.items.0.unit_price_cents", 2500)
        ->assertJsonPath("data.items.0.line_total_cents", 5000)
        ->assertJsonPath("data.items.0.product.id", $product->id)
        ->assertJsonPath("data.items.0.product.name", "Mechanical Keyboard")
        ->assertJsonPath("data.items.0.product.main_image", "https://example.com/products/keyboard.jpg");
});

it("prevents guests from viewing carts", function () {
    $response = getJson("/api/cart");

    $response->assertUnauthorized();
});
