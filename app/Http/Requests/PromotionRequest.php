<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PromotionRequest extends FormRequest
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
            'product_category_id' => 'required|integer|exists:product_categories,id',
            'price' => 'required|numeric|min:0',
            'status' => 'required|in:0,1',
            // 'is_uom_small' => 'required|in:0,1',
            // 'pieces_per_box' => 'required_if:is_uom_small,1|nullable|string|max:255',
            'images' => 'required|array',
            'promotion_end_date' => 'required|date',
            // 'product_ids' => 'required|array',
            // 'product_ids.*' => 'integer|exists:products,id',
            'promotion_products' => 'required',
            'ar_name' => 'required|string|max:255',
            'ar_description' => 'nullable|string',
        ];
    }
}
