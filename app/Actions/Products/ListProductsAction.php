<?php

namespace App\Actions\Products;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProductsAction
{
    public function execute(?string $category = null): LengthAwarePaginator
    {
        return Product::query()
            ->with(["category", "seller", "images"])
            ->where("status", ProductStatus::Active)
            ->when($category, function ($query, $category) {
                $query->whereHas("category", function ($query) use ($category) {
                    $query->where("slug", $category);
                });
            })
            ->latest()
            ->paginate(15);
    }
}
