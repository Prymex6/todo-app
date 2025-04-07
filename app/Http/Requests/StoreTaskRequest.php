<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Autoryzacja jest obsługiwana przez TaskPolicy
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:2000',
            'priority' => [
                'required',
                Rule::in(['low', 'medium', 'high']),
            ],
            'status' => [
                'required',
                Rule::in(['to-do', 'in-progress', 'done']),
            ],
            'due_date' => 'required|date|after_or_equal:today',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'due_date.after_or_equal' => 'The due date must be today or in the future.',
            'priority.in' => 'Invalid priority value. Allowed values: low, medium, high.',
            'status.in' => 'Invalid status value. Allowed values: to-do, in-progress, done.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'due_date' => $this->due_date ?: now()->addDay()->startOfDay(),
        ]);
    }
}