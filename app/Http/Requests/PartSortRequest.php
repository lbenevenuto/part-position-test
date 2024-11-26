<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PartSortRequest extends FormRequest
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
            'positions' => [
                'required',      // Ensure the field exists
                'array',         // Ensure it is an array
                'min:1',         // Ensure at least one element exists
            ],
            'positions.*' => [
                'bail',            // Stop further validations if the current rule fails
                'distinct',        // Ensure all elements in the array are unique
                'exists:parts,id', // Ensure each element exists in `id` column of `parts` table
            ],
        ];
    }
}
