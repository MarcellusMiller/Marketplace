<?php

namespace App\Http\Requests\Api\Cart;

use App\Models\CartItem;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCartItemRequest extends FormRequest
{

    public function authorize(): bool
    {
        /** @var CartItem|null $cartItem */
        $cartItem = $this->route("cartItem");

        return $cartItem !== null
            && $cartItem->cart?->user_id === $this->user()?->id;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "quantity" => ["required", "integer", "min:1"],
        ];
    }
}
