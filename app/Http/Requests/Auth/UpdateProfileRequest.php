<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ConvertArabicNumbers;
use App\Traits\UploadTrait;

class UpdateProfileRequest extends FormRequest
{
    use ConvertArabicNumbers, UploadTrait;

    protected function prepareForValidation()
    {
        if ($this->has('phone')) {
            $this->merge([
                'phone' => $this->arabicToEnglishNumbers($this->phone),
            ]);
        }
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
        $userId = $this->user()?->id;

        return [
            'name'           => ['sometimes', 'string', 'max:255'],
            'phone'          => ['sometimes', 'string', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'unique:users,phone,' . $userId],
            'email'          => ['sometimes', 'email', 'unique:users,email,' . $userId],
            'country_id'     => ['nullable', 'exists:countries,id'],
            'governorate_id' => ['nullable', 'exists:governorates,id'],
            'city_id'        => ['nullable', 'exists:cities,id'],
            'gender'         => ['nullable', 'in:male,female'],
            'nationality_id' => ['nullable', 'exists:nationalities,id'],
            'avatar'         => $this->imageRules(),
            'fcm_token'      => ['nullable', 'string'],
        ];
    }
}
