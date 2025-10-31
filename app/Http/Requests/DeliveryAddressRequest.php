<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DeliveryAddressRequest extends FormRequest
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
            'is_link' => 'required|in:0,1',
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
        ];
    }
}
