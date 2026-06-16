<?php

namespace App\Actions\Products;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProductsAction
{
    public function execute(
        ?string $category = null,
        ?string $search = null,
        ?int $minPrice = null,
        ?int $maxPrice = null,
        ?string $sort = null,
    ): LengthAwarePaginator {
        $query = Product::query()
            ->with(["category", "seller", "images"])
            ->where("status", ProductStatus::Active)
            ->when($category, function ($query, $category) {
                $query->whereHas("category", function ($query) use ($category) {
                    $query->where("slug", $category);
                });
            })
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where("name", "like", "%{$search}%")
                        ->orWhere("description", "like", "%{$search}%");
                });
            })
            ->when($minPrice !== null, function ($query) use ($minPrice) {
                $query->where("price_cents", ">=", $minPrice);
            })
            ->when($maxPrice !== null, function ($query) use ($maxPrice) {
                $query->where("price_cents", "<=", $maxPrice);
            });
        match ($sort) {
            "price_asc" => $query->orderBy("price_cents", "asc"),
            "price_desc" => $query->orderBy("price_cents", "desc"),
            default => $query->latest(),
        };

        return $query->paginate(15);
    }
}
