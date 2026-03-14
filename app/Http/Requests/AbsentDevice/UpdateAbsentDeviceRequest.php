<?php

namespace App\Http\Requests\AbsentDevice;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\ValidationRules\Rules\Delimited;

class UpdateAbsentDeviceRequest extends FormRequest
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
            'absent_device_id' => 'required|string|max:255',
            'publish_topic' => ['required', (new Delimited('regex:/^[a-zA-Z0-9-_]+$/'))
                ->separatedBy('/')
                ->doNotTrimItems()
                ->min(2)
                ->max(7), 'max:255'],
            'subscribe_topic' => ['required', (new Delimited('regex:/^[a-zA-Z0-9-_]+$/'))
                ->separatedBy('/')
                ->doNotTrimItems()
                ->min(2)
                ->max(7), 'max:255'],
        ];
    }
}
