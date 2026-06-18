<?php

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

it("lists public categories ordered by name", function () {
    /** @var Category $books */
    $books = Category::factory()->create([
        "name" => "Books",
        "slug" => "books",
    ]);

    /** @var Category $electronics */
    $electronics = Category::factory()->create([
        "name" => "Electronics",
        "slug" => "electronics",
    ]);

    $response = getJson("/api/categories");

    $response
        ->assertOk()
        ->assertJsonCount(2, "data")
        ->assertJsonStructure([
            "data" => [
                [
                    "id",
                    "name",
                    "slug",
                ],
            ],
        ])
        ->assertJsonPath("data.0.id", $books->id)
        ->assertJsonPath("data.1.id", $electronics->id);
});
