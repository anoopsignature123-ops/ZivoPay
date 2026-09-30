<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;

class ChangePasswordRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
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
            'current_password.required' => 'Please enter your current password.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'New password must be at least 6 characters long.',
            'password.confirmed' => 'New password confirmation does not match.',
        ];
    }
}
