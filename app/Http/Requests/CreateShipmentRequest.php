<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateShipmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // From address
            'from_name' => ['required', 'string', 'max:255'],
            'from_street1' => ['required', 'string', 'max:255'],
            'from_street2' => ['nullable', 'string', 'max:255'],
            'from_city' => ['required', 'string', 'max:255'],
            'from_state' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'from_zip' => ['required', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'from_country' => ['sometimes', 'string', 'in:US'],
            'from_phone' => ['nullable', 'string', 'max:20'],
            'from_email' => ['nullable', 'email', 'max:255'],

            // To address
            'to_name' => ['required', 'string', 'max:255'],
            'to_street1' => ['required', 'string', 'max:255'],
            'to_street2' => ['nullable', 'string', 'max:255'],
            'to_city' => ['required', 'string', 'max:255'],
            'to_state' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'to_zip' => ['required', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'to_country' => ['sometimes', 'string', 'in:US'],
            'to_phone' => ['nullable', 'string', 'max:20'],
            'to_email' => ['nullable', 'email', 'max:255'],

            // Package details
            'weight' => ['required', 'numeric', 'min:0.1', 'max:150'], // ounces
            'length' => ['nullable', 'numeric', 'min:0.1', 'max:100'], // inches
            'width' => ['nullable', 'numeric', 'min:0.1', 'max:100'], // inches
            'height' => ['nullable', 'numeric', 'min:0.1', 'max:100'], // inches
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'from_state.regex' => 'The from state must be a valid 2-letter US state code (e.g., CA, NY).',
            'to_state.regex' => 'The to state must be a valid 2-letter US state code (e.g., CA, NY).',
            'from_zip.regex' => 'The from ZIP code must be in the format 12345 or 12345-6789.',
            'to_zip.regex' => 'The to ZIP code must be in the format 12345 or 12345-6789.',
            'from_country.in' => 'Only US addresses are supported for the from address.',
            'to_country.in' => 'Only US addresses are supported for the to address.',
            'weight.required' => 'Package weight is required.',
            'weight.min' => 'Package weight must be at least 0.1 ounces.',
            'weight.max' => 'Package weight cannot exceed 150 ounces.',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'from_name' => 'from name',
            'from_street1' => 'from street address',
            'from_street2' => 'from apartment/suite',
            'from_city' => 'from city',
            'from_state' => 'from state',
            'from_zip' => 'from ZIP code',
            'from_phone' => 'from phone',
            'from_email' => 'from email',
            'to_name' => 'to name',
            'to_street1' => 'to street address',
            'to_street2' => 'to apartment/suite',
            'to_city' => 'to city',
            'to_state' => 'to state',
            'to_zip' => 'to ZIP code',
            'to_phone' => 'to phone',
            'to_email' => 'to email',
        ];
    }
}
