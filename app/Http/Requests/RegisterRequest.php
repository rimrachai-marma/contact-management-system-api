<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;


class RegisterRequest extends FormRequest {
    public function authorize(): bool {
        return true;
    }

    public function rules(): array {
        return [
            "name" => "required|string|max:255",
            "email" => "required|email",
            "password" => [
                "required",
                "confirmed",
                Password::min(8)->max(16)->mixedCase()->numbers()->symbols(),
            ],
        ];
    }

    public function messages(): array {
        return [
            'name.required' => 'Name field is required.',
            'name.max' => 'Name must not be greater than 255 characters.',

            'email.required' => 'Email field is required.',
            'email.email' => 'Invalid email.',

            'password.required' => 'Password field is required.',
            'password.min' => 'Password must be at least 8 characters.',
            'password.max' => 'Password must not be greater than 16 characters.', 
            'password.mixed' => 'Password must contain both uppercase and lowercase letters.',
            'password.numbers' => 'Password must contain at least one number.',
            'password.symbols' => 'Password must contain at least one special character.',
            'password.confirmed' => 'Password confirmation does not match.',

        ];
    }

     protected function passedValidation(): void {
        if (DB::table('users')->where('email', $this->email)->exists()) {
            throw new HttpResponseException(response()->json([
                'status' => 'error',
                'message' => 'This email is already registered.',
            ], 409));
        }
    }

    protected function failedValidation(Validator $validator): void {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }

    
}

