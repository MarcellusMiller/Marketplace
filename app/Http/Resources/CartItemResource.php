<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $lineTotalCents = $this->quantity * $this->unit_price_cents;

        return [
            "id" => $this->id,
            "quantity" => $this->quantity,
            "unit_price_cents" => $this->unit_price_cents,
            "line_total_cents" => $lineTotalCents,
            "product" => [
                "id" => $this->product->id,
                "name" => $this->product->name,
                "slug" => $this->product->slug,
                "price_cents" => $this->product->price_cents,
                "stock" => $this->product->stock,
                "status" => $this->product->status->value,
                "category" => [
                    "id" => $this->product->category->id,
                    "name" => $this->product->category->name,
                    "slug" => $this->product->category->name,
                ],
                "main_image" => $this->product->images
                    ->firstWhere("is_main", true)?->url,
            ],
        ];
    }
}
