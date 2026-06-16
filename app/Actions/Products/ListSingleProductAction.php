<?php

namespace App\Actions\Products;

use App\Enums\ProductStatus;
use App\Models\Product;

class ListSingleProductAction
{
    public function execute(Product $product): Product
    {
        abort_unless($product->status === ProductStatus::Active, 404);

        return $product->load([
            "category",
            "seller",
            "images",
        ]);
    }
}
