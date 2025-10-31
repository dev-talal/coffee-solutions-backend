<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreStaffRequest extends FormRequest
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
            'first_name' => 'required|string',
            'last_name'  => 'required|string',
            'email'      => [
                'required',
                'email',
                $isUpdate
                    ? 'unique:users,email,' . $userId
                    : 'unique:users,email',
            ],
            'phone'    => 'required|string',
            'role'     => $isUpdate ? 'nullable|exists:roles,name' : 'required|exists:roles,name',
            'location' => 'required',
            'password' => $isUpdate ? 'nullable|string|min:6' : 'required|string|min:6',
            'employe_number' => 'required|string',
            'warehouse_ids' => 'required|array', 
            'warehouse_ids.*' => 'required|exists:ware_houses,id',
        ];
    }

}
