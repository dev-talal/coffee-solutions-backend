<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'code' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'product_category_id' => ['required','integer',Rule::exists('product_categories', 'id')->whereNotNull('parent_id')],
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
            'is_uom_small' => 'required|in:0,1',
            'pieces_per_box' => 'required_if:is_uom_small,1|nullable|string|max:255',
            'images' => 'required|array',
            'ar_name' => 'nullable|string|max:255',
            'ar_description' => 'nullable|string',
            'product_unit_id' => 'nullable|integer|exists:product_units,id',
            'uom_product_unit_id' => 'required_if:is_uom_small,1|integer|exists:product_units,id',
            // 'customer_category_ids' => 'nullable|array',
            // 'customer_category_ids.*' => 'integer|exists:customer_categories,id',
            // 'cutomer_category_id_price' => 'nullable|array',
            // 'cutomer_category_id_price.*' => 'numeric|min:0',
        ];
    }
}
