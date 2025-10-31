<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

class CustomerRequest extends FormRequest
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
    public function rules(Request $request): array
    {
        $userId = $this->route('customer'); 

        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'ar_company_name' => 'nullable|string|max:255',
            'email' => $isUpdate ? 'required|email|max:255|unique:users,email,' . $userId : 'required|email|max:255|unique:users,email',
            'password' => $isUpdate ? 'nullable|string' : 'required|string',
            'customer_code' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255',
            'vat_number' => 'required|string|max:255',
            'region_id' => 'required|exists:regions,id',
            'city_id' => 'required|exists:cities,id',
            'warehouse_id' => 'required|exists:ware_houses,id',
            'delivery_address' => 'nullable|string|max:255',
            'phone' => 'required|string|max:255',
            'customer_category_id' => 'required|exists:customer_categories,id',
            'customer_care_id' => 'required|string|max:255|exists:users,id',
            'sales_id' => 'required|string|max:255|exists:users,id',
            'credit_limit' => 'required|numeric',
            'is_link' => 'required|in:0,1,2',
            'short_address' => 'required_if:is_link,0|string',
            'building_number' => 'required_if:is_link,0|string',
            'secondary_number' => 'required_if:is_link,0|string',
            'postal_code' => 'required_if:is_link,0|string',
            'city' => 'required_if:is_link,0|string',
            'address_link' => 'required_if:is_link,1|string',
            'ar_short_address' => 'required_if:is_link,0|string',
            'ar_building_number' => 'required_if:is_link,0|string',
            'ar_secondary_number' => 'required_if:is_link,0|string',
            'ar_postal_code' => 'required_if:is_link,0|string',
            'ar_city' => 'required_if:is_link,0|string',
            'address_id' => 'nullable|exists:user_delivery_addresses,id',
        ];
    }
}
