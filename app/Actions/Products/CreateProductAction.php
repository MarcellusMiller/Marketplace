<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Str;

class CreateProductAction
{
    /**
     * @params array<string, mixed> $data
     */
    public function execute(User $seller, array $data): Product
    {
        $product = Product::create([
            "seller_id" => $seller->id,
            "category_id" => $data["category_id"],
            "name" => $data["name"],
            "slug" => Str::slug($data["name"]),
            "description" => $data["description"] ?? null,
            "price_cents" => $data["price_cents"],
            "stock" => $data["stock"],
            "status" => $data["status"],
        ]);

        return $product->load([
            "category",
            "seller",
            "images",
        ]);
    }
}
