<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;

class RechargeRequest extends BaseApiRequest
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
        return [
            'operator_code' => 'required|string|max:50',
            'circle_code' => 'nullable|string|max:20',
            'number' => 'required|string|min:8|max:50',
            'value1' => 'nullable|string|max:100',
            'value2' => 'nullable|string|max:100',
            'amount' => 'required|numeric|min:10|max:50000',
            'service_type' => 'nullable|string|in:mobile,dth,postpaid,electricity,gas,fastag,insurance,voucher,google_play',
        ];
    }

    /**
     * Get custom validation messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'operator_code.required' => 'Please select an operator.',
            'number.required' => 'Please enter the mobile, customer ID, or consumer number.',
            'amount.required' => 'Please enter a valid recharge amount.',
            'amount.min' => 'Minimum recharge amount is ₹10.00.',
            'amount.max' => 'Maximum recharge amount is ₹50,000.00.',
        ];
    }
}
