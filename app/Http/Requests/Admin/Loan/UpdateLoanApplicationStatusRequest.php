<?php

namespace App\Http\Requests\Admin\Loan;

use App\Models\LoanApplication;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLoanApplicationStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    public function rules(): array
    {
        return ['status' => ['required', Rule::in(LoanApplication::STATUSES)], 'admin_remarks' => ['nullable', 'string', 'max:5000']];
    }
}
