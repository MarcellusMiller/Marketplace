<?php

namespace App\Actions\Cart;

use App\Enums\ProductStatus;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class AddCartItemAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(User $user, array $data): Cart
    {
        $cart = Cart::firstOrCreate([
            "user_id" => $user->id,
        ]);

        /** @var Product $product */
        $product = Product::query()->findOrFail($data["product_id"]);

        if($product->status !== ProductStatus::Active) {
            throw ValidationException::withMessages([
                "product_id" => "The selected product is not available",
            ]);
        }

        $item = $cart->items()
            ->where("product_id",$product->id)
            ->first();

        $requestedQuantity = (int) $data["quantity"];
        $currentQuantity = $item?->quantity ?? 0;
        $newQuantity = $currentQuantity + $requestedQuantity;

        if($newQuantity > $product->stock) {
            throw ValidationException::withMessages([
                "quantity" => "The requested quantity exceeds the available stock",
            ]);
        }

        if($item) {
            $item->update([
                "quantity" => $newQuantity,
            ]);
        } else {
            $cart->items()->create([
                "product_id" => $product->id,
                "quantity" => $requestedQuantity,
                "unit_price_cents" => $product->price_cents,
            ]);
        }

        return $cart->load([
            "items.product.category",
            "items.product.images",
        ]);
    }
}
