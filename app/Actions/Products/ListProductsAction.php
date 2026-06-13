<?php

namespace App\Actions\Products;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ListProductsAction
{
    public function execute(): LengthAwarePaginator
    {
        return Product::query()
            ->with(["category", "seller", "images"])
            ->where("status", ProductStatus::Active)
            ->latest()
            ->paginate(15);
    }
}
