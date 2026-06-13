<?php

use App\Enums\ProductStatus;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
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

it("filters products by category", function () {
    $categoryA = Category::factory()->create([
        "name" => "Category A",
        "slug" => "category-a",
    ]);

    $categoryB = Category::factory()->create([
        "name" => "Category B",
        "slug" => "category-b",
    ]);

    $productA = Product::factory()->create([
        "name" => "Product A",
        "slug" => "product-a",
        "status" => ProductStatus::Active,
        "category_id" => $categoryA->id,
    ]);

    Product::factory()->create([
        "name" => "Product B",
        "slug" => "product-b",
        "status" => ProductStatus::Active,
        "category_id" => $categoryB->id,
    ]);

    $response = $this->getJson("/api/products?category=category-a");

    $response
        ->assertOk()
        ->assertJsonCount(1, "data")
        ->assertJsonPath("data.0.id", $productA->id)
        ->assertJsonPath("data.0.category.slug", "category-a");
});

it("filters products by search term", function () {
    $matchingProduct = Product::factory()->create([
        "name" => "Notebook Gamer",
        "description" => "High performance product",
        "status" => ProductStatus::Active,
    ]);

    Product::factory()->create([
        "name" => "Office Chair",
        "description" => "Comfortable chair",
        "status" => ProductStatus::Active,
    ]);

    $response = $this->getJson("/api/products?search=notebook");

    $response
        ->assertOk()
        ->assertJsonCount(1, "data")
        ->assertJsonPath("data.0.id", $matchingProduct->id);
});

it("filters products by description search term", function () {
    $matchingProduct = Product::factory()->create([
        "name" => "Generic Product",
        "description" => "Contains the word ergonomic",
        "status" => ProductStatus::Active,
    ]);

    Product::factory()->create([
        "name" => "Another Product",
        "description" => "Does not match",
        "status" => ProductStatus::Active,
    ]);

    $response = $this->getJson("/api/products?search=ergonomic");

    $response
        ->assertOk()
        ->assertJsonCount(1, "data")
        ->assertJsonPath("data.0.id", $matchingProduct->id);
});

it("filters products by minimum price", function () {
    $matchingProduct = Product::factory()->create([
        "price_cents" => 5000,
        "status" => ProductStatus::Active,
    ]);

    Product::factory()->create([
        "price_cents" => 900,
        "status" => ProductStatus::Active,
    ]);

    $response = $this->getJson("/api/products?min_price=1000");

    $response
        ->assertOk()
        ->assertJsonCount(1, "data")
        ->assertJsonPath("data.0.id", $matchingProduct->id);
});

it("filters products by maximum price", function () {
    $matchingProduct = Product::factory()->create([
        "price_cents" => 900,
        "status" => ProductStatus::Active,
    ]);

    Product::factory()->create([
        "price_cents" => 5000,
        "status" => ProductStatus::Active,
    ]);

    $response = $this->getJson("/api/products?max_price=1000");

    $response
        ->assertOk()
        ->assertJsonCount(1, "data")
        ->assertJsonPath("data.0.id", $matchingProduct->id);
});

it("filters products by price range", function () {
    $matchingProduct = Product::factory()->create([
        "price_cents" => 2500,
        "status" => ProductStatus::Active,
    ]);

    Product::factory()->create([
        "price_cents" => 900,
        "status" => ProductStatus::Active,
    ]);

    Product::factory()->create([
        "price_cents" => 5000,
        "status" => ProductStatus::Active,
    ]);

    $response = $this->getJson("/api/products?min_price=1000&max_price=3000");

    $response
        ->assertOk()
        ->assertJsonCount(1, "data")
        ->assertJsonPath("data.0.id", $matchingProduct->id);
});