<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CityRequest extends FormRequest
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
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');
        return [
            'name' =>   'required|string|max:255',
            'ar_name' => 'required|string|max:255',
            'status' => 'required|in:0,1',
            'region_id' => $isUpdate ? 'sometimes|required|exists:regions,id' : 'required|exists:regions,id',
        ];
    }
}
