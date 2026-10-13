<?php

namespace App\Http\Requests\Api;

use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class RegisterRequest extends BaseApiRequest
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $sponsor = $this->input('sponsor_code') ?? $this->input('sponsor_id') ?? $this->input('sponsor');

        if ($sponsor !== null) {
            $sponsor = trim((string) $sponsor);
        }

        $this->merge([
            'sponsor_code' => ! empty($sponsor) ? $sponsor : null,
        ]);
    }

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
            'sponsor_code' => [
                'nullable',
                'string',
                function (string $attribute, mixed $value, Closure $fail): void {
                    if (in_array($value, ['ZIVO-0000001', 'DEX-0000001'], true)) {
                        return;
                    }
                    if (! User::where('referral_code', $value)->exists()) {
                        $fail('The provided sponsor referral code is invalid.');
                    }
                },
            ],
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
