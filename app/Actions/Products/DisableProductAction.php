<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Enums\ProductStatus;

class DisableProductAction
{
    public function execute(Product $product): Product
    {
        $product->update([
            "status" => ProductStatus::Disabled,
        ]);

        return $product->load([
            "category",
            "seller",
            "images",
        ]);
    }
}
