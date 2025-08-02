<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            'email' => 'required',
            'password' => 'required',
        ];
    }

    public function messages(): array {
        return [
            'email.required' => 'Email field is required.',
            'password.required' => 'Password field is required.',
        ];
    }

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Please fill in missing fields.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
