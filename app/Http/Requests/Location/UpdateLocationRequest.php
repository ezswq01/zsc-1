<?php

namespace App\Http\Requests\Location;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLocationRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'code'         => 'required|string|max:100|unique:locations,code,' . $this->route('location'),
            'company_name' => 'nullable|string|max:255',
            'name'         => 'nullable|string|max:255',
            'address'      => 'nullable|string',
            'city'         => 'nullable|string|max:100',
            'coordinate'   => ['nullable', 'string', 'regex:/^-?\d{1,2}(\.\d+)?,\s*-?\d{1,3}(\.\d+)?$/'],
            'is_active'    => 'boolean',
        ];
    }
}
