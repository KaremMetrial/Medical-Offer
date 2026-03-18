<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCardRequest extends FormRequest
{
    protected $stopOnFirstFailure = true;

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
            'governorate_id' => 'required|exists:governorates,id',
            'city_id'        => 'required|exists:cities,id',
            'address'        => 'required|string',
            'receiver_name'  => 'nullable|string|max:255',
            'receiver_phone' => 'nullable|string|max:20',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'governorate_id' => __('message.governorate'),
            'city_id'        => __('message.city'),
            'address'        => __('message.address'),
            'receiver_name'  => __('message.receiver_name'),
            'receiver_phone' => __('message.receiver_phone'),
        ];
    }
}
