<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "slug" => $this->slug,
            "description" => $this->description,
            "price_cents" => $this->price_cents,
            "stock" => $this->stock,
            "status" => $this->status->value,
            "category" => [
                "id" => $this->category->id,
                "name" => $this->category->name,
                "slug" => $this->category->slug,
            ],
            "seller" => [
                "id" => $this->seller->id,
                "name" => $this->seller->name,
            ],
            "main_image" => $this->images
                ->firstWhere("is_main", true)?->url,
        ];
    }
}
