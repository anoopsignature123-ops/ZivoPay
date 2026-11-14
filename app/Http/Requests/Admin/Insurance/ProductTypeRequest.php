<?php

namespace App\Http\Requests\Admin\Insurance;

use App\Models\InsuranceCompany;
use App\Models\InsuranceProduct;
use App\Models\ProductType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductTypeRequest extends FormRequest
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
        $productType = $this->route('product_type');

        return [
            'insurance_category_id' => ['required', 'integer', 'exists:insurance_categories,id'],
            'insurance_company_id' => ['required', 'integer', 'exists:insurance_companies,id'],
            'insurance_product_id' => ['required', 'integer', 'exists:insurance_products,id'],
            'name' => ['required', 'string', 'max:191'],
            'slug' => ['nullable', 'string', 'max:191', Rule::unique('product_types', 'slug')->ignore($productType instanceof ProductType ? $productType->id : null)],
            'description' => ['nullable', 'string', 'max:5000'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:4294967295'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /** @return array<int, callable(Validator): void> */
    public function after(): array
    {
        return [function (Validator $validator): void {
            if ($validator->errors()->hasAny(['insurance_category_id', 'insurance_company_id', 'insurance_product_id'])) {
                return;
            }

            $categoryId = $this->input('insurance_category_id');
            $companyId = $this->input('insurance_company_id');
            $productId = $this->input('insurance_product_id');

            if ($companyId && $categoryId && ! InsuranceCompany::query()->whereKey($companyId)->where('insurance_category_id', $categoryId)->exists()) {
                $validator->errors()->add('insurance_company_id', 'The selected company does not belong to the selected category.');
            }

            if ($productId) {
                $product = InsuranceProduct::query()->find($productId);
                if ($product && $companyId && (int) $product->insurance_company_id !== (int) $companyId) {
                    $validator->errors()->add('insurance_product_id', 'The selected product does not belong to the selected company.');
                }
                if ($product && $categoryId && (int) $product->insurance_category_id !== (int) $categoryId) {
                    $validator->errors()->add('insurance_product_id', 'The selected product does not belong to the selected category.');
                }
            }
        }];
    }
}
