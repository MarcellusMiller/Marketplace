<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("retrieves a product active for slug", function () {
    $product = Product::factory()->create([
        "name" => "Test Product",
        "slug" => "test-product",
        "description" => "A product created for detail tests.",
        "price_cents" => 2590,
        "stock" => 12,
        "status" => ProductStatus::Active,
    ]);

    ProductImage::factory()
        ->for($product)
        ->main()
        ->create([
            "url" => "https://example.com/products/test-product.jpg",
        ]);

    $response = $this->getJson("/api/products/{$product->slug}");

    $response
        ->assertOk()
        ->assertJsonStructure([
            "data" => [
                "id",
                "name",
                "slug",
                "description",
                "price_cents",
                "stock",
                "status",
                "category" => [
                    "id",
                    "name",
                    "slug",
                ],
                "seller" => [
                    "id",
                    "name",
                ],
                "images" => [
                    [
                        "id",
                        "url",
                        "is_main",
                    ],
                ],
            ],
        ])
        ->assertJsonPath("data.id", $product->id)
        ->assertJsonPath("data.slug", "test-product")
        ->assertJsonPath("data.status", "active");
});

it("returns images for product details", function () {
    $product = Product::factory()->create([
        "name" => "Test Product",
        "slug" => "test-product",
        "description" => "A product created for detail tests.",
        "price_cents" => 2590,
        "stock" => 12,
        "status" => ProductStatus::Active,
    ]);

    ProductImage::factory()
        ->for($product)
        ->main()
        ->create([
            "url" => "https://example.com/products/test-product.jpg",
        ]);

    ProductImage::factory()
        ->for($product)
        ->create([
            "url" => "https://example.com/products/test-product-2.jpg",
            "is_main" => false,
        ]);

    $response = $this->getJson("/api/products/{$product->slug}");

    $response
        ->assertOk()
        ->assertJsonCount(2, "data.images")
        ->assertJsonPath("data.images.0.url", "https://example.com/products/test-product.jpg")
        ->assertJsonPath("data.images.1.url", "https://example.com/products/test-product-2.jpg");
});

it("returns 404 for inactive product", function () {
    $product = Product::factory()->create([
        "name" => "Test Product",
        "slug" => "test-product",
        "description" => "A product created for detail tests.",
        "price_cents" => 2590,
        "stock" => 12,
        "status" => ProductStatus::Inactive,
    ]);

    $response = $this->getJson("/api/products/{$product->slug}");

    $response->assertNotFound();
});

it("returns 404 for non-existing product", function () {
    $response = $this->getJson("/api/products/non-existing-product");

    $response->assertNotFound();
});
