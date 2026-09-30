<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;

class UpdateProfileRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'name' => 'nullable|string|max:255',
            'mobile' => 'nullable|string|max:20|unique:users,mobile,'.$userId,
            'wallet_address' => 'nullable|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:6|confirmed',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'mobile.unique' => 'This mobile number is already in use by another member account.',
            'image.image' => 'Profile avatar must be a valid image file (JPG, PNG, WEBP).',
            'image.max' => 'Profile image size must not exceed 5MB.',
            'profile_image.image' => 'Profile avatar must be a valid image file (JPG, PNG, WEBP).',
            'profile_image.max' => 'Profile image size must not exceed 5MB.',
            'password.min' => 'New password must be at least 6 characters.',
            'password.confirmed' => 'New password confirmation does not match.',
        ];
    }
}
