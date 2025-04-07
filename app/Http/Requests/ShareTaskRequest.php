<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ShareTaskRequest extends FormRequest
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
            'email' => 'required|email|max:255',
            'expiry_days' => 'required|integer|min:1|max:30',
            'max_uses' => 'nullable|integer|min:1|max:100',
            'allow_editing' => 'required|boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'expiry_days.max' => 'The share link cannot expire later than 30 days from now.',
            'max_uses.max' => 'The maximum usage limit is 100.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'expiry_days' => (int)$this->expiry_days,
            'max_uses' => $this->max_uses ? (int)$this->max_uses : null,
            'allow_editing' => (bool)$this->allow_editing,
        ]);
    }
}