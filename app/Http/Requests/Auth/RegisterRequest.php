<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ConvertArabicNumbers;
use App\Traits\UploadTrait;

class RegisterRequest extends FormRequest
{
    use ConvertArabicNumbers, UploadTrait;

    protected function prepareForValidation()
    {
        $this->merge([
            'phone' => $this->arabicToEnglishNumbers($this->phone),
        ]);
    }
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
            'name'       => ['required', 'string', 'max:255'],
            'phone'      => ['required', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'exists:users,phone'],
            'email'      => ['nullable', 'email', 'unique:users,email'],
            'password'   => ['nullable', 'string', 'min:8', 'confirmed'],
            'country_id' => ['nullable', 'exists:countries,id'],
            'city_id'    => ['nullable', 'exists:cities,id'],
            'avatar'     => $this->imageRules(),
        ];
    }
}
