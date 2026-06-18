<?php

namespace App\Actions\Cart;

use App\Models\Cart;
use App\Models\CartItem;

class RemoveCartItemAction
{
    public function execute(CartItem $cartItem): Cart
    {
        /** @var Cart $cart */
        $cart = $cartItem->cart;

        $cartItem->delete();

        return $cart->load([
            "items.product.category",
            "items.product.images",
        ]);
    }
}
