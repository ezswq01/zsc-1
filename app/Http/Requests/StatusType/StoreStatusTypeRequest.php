<?php

namespace App\Http\Requests\StatusType;

use Illuminate\Foundation\Http\FormRequest;

class StoreStatusTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'color' => 'required|string|max:255'
        ];
    }
}
