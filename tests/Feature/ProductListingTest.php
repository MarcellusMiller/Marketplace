<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it("lists active products with pagination data", function () {
    $activeProduct = Product::factory()->create([
        "name" => "Test Product",
        "slug" => "test-product",
        "description" => "A product created for listing tests.",
        "price_cents" => 2590,
        "stock" => 12,
        "status" => ProductStatus::Active,
    ]);

    ProductImage::factory()
        ->for($activeProduct)
        ->main()
        ->create([
            "url" => "https://example.com/products/test-product.jpg",
        ]);

    Product::factory()->create([
        "status" => ProductStatus::Inactive,
    ]);

    $response = $this->getJson("/api/products");

    $response
        ->assertOk()
        ->assertJsonCount(1, "data")
        ->assertJsonStructure([
            "data" => [
                [
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
                    "main_image",
                ],
            ],
            "links",
            "meta",
        ])
        ->assertJsonPath("data.0.id", $activeProduct->id)
        ->assertJsonPath("data.0.status", "active")
        ->assertJsonPath("data.0.main_image", "https://example.com/products/test-product.jpg");
});
