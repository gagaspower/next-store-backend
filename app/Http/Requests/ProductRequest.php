<?php

namespace App\Http\Requests;

use App\Traits\ApiValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    use ApiValidationResponse;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        if ($this->route('id')) {
            return [
                'product_name'        => 'required',
                'product_sku'         => 'required|unique:product,product_sku,' . $this->route('id'),
                'product_category_id' => 'required',
                'product_stock'       => 'nullable|numeric',
                'product_price'       => 'nullable|numeric',
                'product_weight'      => 'required|numeric',
                'product_image'       => 'nullable|image|mimes:jpeg,png,jpg|max:3072',
            ];
        } else {
            return [
                'product_name'        => 'required',
                'product_sku'         => 'required|unique:product',
                'product_category_id' => 'required',
                'product_stock'       => 'nullable|numeric',
                'product_price'       => 'nullable|numeric',
                'product_weight'      => 'required|numeric',
                'product_image'       => 'required|image|mimes:jpeg,png,jpg|max:3072',
            ];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedResponse($validator);
    }
}
