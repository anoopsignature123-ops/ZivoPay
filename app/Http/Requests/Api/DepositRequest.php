<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;

class DepositRequest extends BaseApiRequest
{
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
        $minDeposit = config('gateway.min_deposit', 100.00);
        $maxDeposit = config('gateway.max_deposit', 500000.00);

        return [
            'amount' => "required|numeric|min:{$minDeposit}|max:{$maxDeposit}",
            'payment_method' => 'required|string|in:UPI,USDT,Online Gateway',
            'trx_hash' => 'required|string|min:6|max:100|unique:deposits,trx_hash',
            'proof_file' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'remark' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        $minDeposit = config('gateway.min_deposit', 100.00);
        $maxDeposit = config('gateway.max_deposit', 500000.00);

        return [
            'amount.required' => 'Please enter the amount to add.',
            'amount.numeric' => 'Deposit amount must be a valid number.',
            'amount.min' => 'Minimum Add Fund amount is ₹'.number_format($minDeposit, 2).'.',
            'amount.max' => 'Maximum Add Fund amount is ₹'.number_format($maxDeposit, 2).'.',
            'payment_method.required' => 'Please select a valid payment method.',
            'payment_method.in' => 'Selected payment method is invalid. Allowed: UPI, USDT, Online Gateway.',
            'trx_hash.required' => 'Please enter the UTR / Transaction Reference Number / Hash.',
            'trx_hash.unique' => 'This UTR / Transaction Reference Number has already been submitted for verification.',
            'proof_file.image' => 'Payment proof must be a valid image file (JPG, PNG, WEBP).',
            'proof_file.max' => 'Payment proof image size must not exceed 5MB.',
        ];
    }
}