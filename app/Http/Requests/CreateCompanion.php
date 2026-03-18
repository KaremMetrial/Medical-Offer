<?php

namespace App\Http\Requests;

use App\Traits\UploadTrait;
use Illuminate\Foundation\Http\FormRequest;
use App\Traits\ConvertArabicNumbers;

class CreateCompanion extends FormRequest
{
    use UploadTrait, ConvertArabicNumbers;
    protected $stopOnFirstFailure = true;

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
            'name' => 'required|string|max:255',
            'gender' => 'required|in:' . implode(',', \App\Enums\GenderType::values()),
            'phone' => 'required|string|regex:/^([0-9\s\-\+\(\)]*)$/|unique:users,phone',
            'relationship' => 'required|in:' . implode(',', \App\Enums\RelationshipType::values()),
            'attachments' => 'nullable|array',
            'attachments.*.file' => $this->imageRules(),
            'attachments.*.type' => 'required|string|max:50',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $subscription = $user->currentSubscription();

            if (!$subscription) {
                $validator->errors()->add('subscription', __('message.no_active_subscription'));
                return;
            }

            $plan = $subscription->plan;
            $maxBuddies = (int) ($plan->features_json['number_of_buddies'] ?? 0);
            $currentBuddiesCount = $user->children()->count();

            if ($currentBuddiesCount >= $maxBuddies) {
                $validator->errors()->add('limit', __('message.companion_limit_reached'));
            }
        });
    }
}
