<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Str;

class UpdateProductAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(Product $product, array $data): Product
    {
        if(array_key_exists("name", $data)) {
            $data["slug"] = Str::slug($data["name"]);
        }

        $product->update($data);

        return $product->load([
            "category",
            "seller",
            "images",
        ]);
    }
}
