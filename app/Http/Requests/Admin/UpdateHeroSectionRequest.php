<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHeroSectionRequest extends FormRequest
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
            'headline' => ['required', 'string', 'max:255'],
            'subheadline' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'button_text' => ['nullable', 'string', 'max:255'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'headline_translations' => ['sometimes', 'array'],
            'headline_translations.*' => ['nullable', 'string', 'max:255'],
            'subheadline_translations' => ['sometimes', 'array'],
            'subheadline_translations.*' => ['nullable', 'string', 'max:255'],
            'description_translations' => ['sometimes', 'array'],
            'description_translations.*' => ['nullable', 'string'],
            'button_text_translations' => ['sometimes', 'array'],
            'button_text_translations.*' => ['nullable', 'string', 'max:255'],
            'image' => ['sometimes', 'file', 'image', 'max:7168'],
        ];
    }
}
