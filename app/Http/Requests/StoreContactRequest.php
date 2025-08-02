<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreContactRequest extends FormRequest
{
    public function authorize(): bool{
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'phone'      => 'required|string|max:20',
            'email'      => 'nullable|email|max:255',
            'address'    => 'nullable|string|max:1000',
            'dob'        => 'nullable|date',
            'notes'      => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array {
        return [
            'first_name.required' => 'First name field is required.',
            'first_name.max' => 'First name must not be greater than 255 characters.',
            
            'last_name.max' => 'Last name must not be greater than 255 characters.',

            'phone.required' => 'Phone number field is required.',
            'phone.max' => 'Phone number must not be greater than 20 characters.',

            'email.email' => 'Invalid email.',
            'email.max' => 'Email must not be greater than 255 characters.',

            'address.max' => 'Address must not be greater than 1000 characters.',

            'dob.date' => 'Invalid date.',

            'notes.max' => 'Notes must not be greater than 1000 characters.',
        ];
    }

    protected function failedValidation(Validator $validator): void {
        throw new HttpResponseException(response()->json([
            'status' => 'error',
            'message' => 'Validation failed',
            'errors' => $validator->errors(),
        ], 422));
    }
}
