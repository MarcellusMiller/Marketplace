<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Validation\ValidationException;

class UpdateCartItemAction
{
    /**
     * @param array<string, mixed> $data
     */
    public function execute(CartItem $cartItem, array $data): Cart
    {
        $quantity = (int) $data["quantity"];

        if ($quantity > $cartItem->product->stock) {
            throw ValidationException::withMessages([
                "quantity" => "The requested quantity exceeds the available stock.",
            ]);
        }

        $cartItem->update([
            "quantity" => $quantity,
        ]);

        return $cartItem->cart->load([
            "items.product.category",
            "items.product.images",
        ]);
    }
}
