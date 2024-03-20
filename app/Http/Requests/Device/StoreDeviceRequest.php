<?php

namespace App\Http\Requests\Device;

use Illuminate\Foundation\Http\FormRequest;
use Spatie\ValidationRules\Rules\Delimited;

class StoreDeviceRequest extends FormRequest
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
            'device_id' => 'required|string|max:255',
            'sensor_id' => 'required|string|max:255',
            'device_type_id' => 'required|exists:device_types,id',
            'branch' => 'required|string|max:255',
            'building' => 'required|string|max:255',
            'room' => 'required|string|max:255',
            'subscribe_expressions.*.status_type.*' => 'sometimes|exists:status_types,id',

            // OLD CODES
            // 'device_id' => 'required|string|max:255',
            // 'device_type_id' => 'required|exists:device_types,id',
            // 'subscribe_expressions.*.status_type.*' => 'sometimes|exists:status_types,id'
            // 'publish_topic' => ['required', (new Delimited('regex:/^[a-zA-Z0-9-_]+$/'))
            //     ->separatedBy('/')
            //     ->doNotTrimItems()
            //     ->min(2)
            //     ->max(7), 'max:255'],
            // 'subscribe_topic' => ['required', (new Delimited('regex:/^[a-zA-Z0-9-_]+$/'))
            //     ->separatedBy('/')
            //     ->doNotTrimItems()
            //     ->min(2)
            //     ->max(7), 'max:255'],
        ];
    }
}
