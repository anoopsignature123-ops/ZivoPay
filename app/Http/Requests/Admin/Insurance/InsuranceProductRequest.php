<?php

namespace App\Http\Requests\Admin\Insurance;

use App\Models\InsuranceProduct;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InsuranceProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'insurance_category_id' => ['required', 'integer', 'exists:insurance_categories,id'],
            'insurance_company_id' => [
                'required',
                'integer',
                Rule::exists('insurance_companies', 'id')->where('insurance_category_id', $this->input('insurance_category_id')),
            ],
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('insurance_products', 'slug')->ignore($product instanceof InsuranceProduct ? $product->id : null)],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
