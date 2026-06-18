<?php

namespace App\Http\Requests\Api\Products;

use App\Enums\ProductStatus;
use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Product|null $product */
        $product = $this->route("product");

        return $product !== null
            && ($this->user()?->can("update", $product) ?? false);
    }
    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            "category_id" => ["sometimes", "required", "ulid", "exists:categories,id"],
            "name" => ["sometimes", "required", "string", "max:255"],
            "description" => ["sometimes", "nullable", "string"],
            "price_cents" => ["sometimes", "required", "integer", "min:1"],
            "stock" => ["sometimes", "required", "integer", "min:0"],
            "status" => ["sometimes", "required", Rule::enum(ProductStatus::class)],
        ];
    }
}
