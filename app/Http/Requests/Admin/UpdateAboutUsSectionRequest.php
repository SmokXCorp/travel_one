<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutUsSectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'paragraph_one' => ['nullable', 'string'],
            'paragraph_two' => ['nullable', 'string'],
            'paragraph_three' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'title_translations' => ['sometimes', 'array'],
            'title_translations.*' => ['nullable', 'string', 'max:255'],
            'paragraph_one_translations' => ['sometimes', 'array'],
            'paragraph_one_translations.*' => ['nullable', 'string'],
            'paragraph_two_translations' => ['sometimes', 'array'],
            'paragraph_two_translations.*' => ['nullable', 'string'],
            'paragraph_three_translations' => ['sometimes', 'array'],
            'paragraph_three_translations.*' => ['nullable', 'string'],
        ];
    }
}
