<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductCategoryRequest extends FormRequest
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
        $userId = $this->route('staff'); // e.g. for PUT /staff/{staff}

        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'name' => 'required',
            'ar_name' => 'required',
            'parent_id' => 'nullable|integer|exists:product_categories,id' ,
            'icon' => $isUpdate ? 'nullable' : 'required', 
            'status' => 'required|in:0,1',
        ];
    }
}
