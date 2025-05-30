<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerRequest extends FormRequest
{
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
        return [
            'code' => 'required|string|size:11|unique:customers',
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:80',
            'address' => 'required|string|max:100',
            'phone' => 'required|string|max:9',   
            'birthdate' => 'required|date|before:now',
            'client_type_id' => 'required|exists:client_types,id',
        ];
    }
}