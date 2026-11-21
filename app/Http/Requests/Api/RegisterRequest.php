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

        $mobile = $this->input('mobile');
        if ($mobile !== null) {
            $mobile = preg_replace('/[^0-9]/', '', (string) $mobile);
            if (str_starts_with($mobile, '91') && strlen($mobile) === 12) {
                $mobile = substr($mobile, 2);
            }
        }

        $this->merge([
            'sponsor_code' => ! empty($sponsor) ? $sponsor : null,
            'mobile' => $mobile,
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
            'mobile' => ['required', 'string', 'regex:/^[6-9][0-9]{9}$/', 'unique:users,mobile'],
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
            'mobile.regex' => 'Mobile number must be a valid 10-digit number without +91 or country code.',
            'sponsor_code.exists' => 'The provided sponsor referral code is invalid.',
        ];
    }
}
