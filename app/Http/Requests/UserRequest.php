<?php

namespace App\Http\Requests;


use App\Traits\ApiValidationResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;


class UserRequest extends FormRequest
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

        switch ($this->method()) {
            case 'POST':
                return [
                    'name' => 'required',
                    'email' => 'required|email|unique:users',
                    'password' => 'required|alpha_num|min:6|max:8'
                ];
                break;
            case 'PUT':
                return [
                    'name' => 'required',
                    'email' => 'required|email|unique:users,email,' . $this->route('id'),
                    'password' => 'nullable|alpha_num|min:6|max:8'
                ];
                break;
            default:
                return [];
                break;
        }
    }

    protected function failedValidation(Validator $validator)
    {
        $this->failedResponse($validator);
    }
}
