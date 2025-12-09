<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAboutSectionRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description_primary' => ['nullable', 'string'],
            'description_secondary' => ['nullable', 'string'],
            'is_active' => ['sometimes', 'boolean'],
            'title_translations' => ['sometimes', 'array'],
            'title_translations.*' => ['nullable', 'string'],
            'description_primary_translations' => ['sometimes', 'array'],
            'description_primary_translations.*' => ['nullable', 'string'],
            'description_secondary_translations' => ['sometimes', 'array'],
            'description_secondary_translations.*' => ['nullable', 'string'],
            'image_one' => ['sometimes', 'file', 'image', 'max:7168'],
            'image_two' => ['sometimes', 'file', 'image', 'max:7168'],
            'image_three' => ['sometimes', 'file', 'image', 'max:7168'],
        ];
    }
}
