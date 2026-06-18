<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    "cart_id",
    "product_id",
    "quantity",
    "unit_price_cents",
])]
class CartItem extends Model
{
    use HasFactory, HasUlids;

    protected function casts(): array
    {
        return [
            "quantity" => "integer",
            "unit_price_cents" => "integer",
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
