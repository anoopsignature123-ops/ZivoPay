<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;

class RegisterRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'mobile' => 'required|string|max:20|unique:users,mobile',
            'sponsor_code' => 'required|string|exists:users,referral_code',
            'position' => 'nullable|string|in:left,right,direct',
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
            'email.unique' => 'This email address is already registered.',
            'mobile.unique' => 'This mobile number is already registered.',
            'sponsor_code.exists' => 'The provided sponsor referral code is invalid.',
        ];
    }
}
