<?php

namespace App\Http\Requests;

use App\Traits\ApiValidationResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class BannerRequest extends FormRequest
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
                'banner_title' => 'required',
                'banner_url'   => 'nullable|url:http,https',
                'banner_image' => 'nullable|image|mimes:jpeg,png,jpg|max:5120',
            ];
        } else {
            return [
                'banner_title' => 'required',
                'banner_url'   => 'nullable|url:http,https',
                'banner_image' => 'required|image|mimes:jpeg,png,jpg|max:5120',
            ];
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedResponse($validator);
    }
}
