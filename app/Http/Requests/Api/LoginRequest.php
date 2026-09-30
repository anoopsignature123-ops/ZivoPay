<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;

class LoginRequest extends BaseApiRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id' => 'required_without_all:login_id,email,mobile|nullable|string',
            'login_id' => 'nullable|string',
            'email' => 'nullable|string',
            'mobile' => 'nullable|string',
            'password' => 'required|string',
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
            'user_id.required' => 'Please enter your User ID, Referral Code, Email, or Mobile number.',
            'password.required' => 'Please enter your password.',
        ];
    }
}
