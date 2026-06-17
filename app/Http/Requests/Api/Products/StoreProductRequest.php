<?php

namespace App\Http\Requests\Api\Products;

use App\Enums\ProductStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can("create", \App\Models\Product::class) ?? false;
    }

    /** 
     * @return array<string, mixed>
     */
    
    public function rules(): array 
    {
        return [
            "category_id" => ["required", "ulid", "exists:categories,id"],
            "name" => ["required", "string", "max:255"],
            "description" => ["nullable", "string"],
            "price_cents" => ["required", "integer", "min:1"],
            "stock" => ["required", "integer", "min:0"],
            "status" => ["required", Rule::enum(ProductStatus::class)],
        ];
    }
}
