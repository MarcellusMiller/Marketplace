<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

#[Fillable([
    "seller_id",
    "category_id",
    "name",
    "slug",
    "description",
    "price_cents",
    "stock",
    "status",
])]
class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory, HasUlids, SoftDeletes;

    protected function casts(): array
    {
        return [
            "status" => ProductStatus::class,
            "price_cents" => "integer",
            "stock" => "integer",
        ];
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, "seller_id");
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
