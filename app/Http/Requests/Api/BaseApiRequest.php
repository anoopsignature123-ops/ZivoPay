<?php

namespace App\Http\Requests\Api;

use App\Traits\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

abstract class BaseApiRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Handle a failed validation attempt for API requests.
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(
            $this->validationErrorResponse(
                $validator->errors(),
                'Validation failed. Please check the input errors.'
            )
        );
    }
}
