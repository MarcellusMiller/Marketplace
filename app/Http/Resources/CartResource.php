<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $subtotalCents = $this->items->sum(function ($item){
            return $item->quantity * $item->unit_price_cents;
        });

        return [
            "id" => $this->id,
            "subtotal_cents" => $subtotalCents,
            "items_count" => $this->items->sum("quantity"),
            "items" => CartItemResource::collection($this->items),
        ];
    }
}
