<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class CreateVisit extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'companion_id' => 'nullable|exists:users,id',
            'offers' => 'required|array',
            'offers.*.id' => 'required|exists:offers,id',
            'offers.*.price' => 'required|numeric|min:0',
            'comment' => 'nullable|string',
        ];
    }
}
