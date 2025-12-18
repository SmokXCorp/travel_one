<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFooterContentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'contact_email' => ['nullable', 'email', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:255'],
            'contact_address' => ['nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'quick_links_translations' => ['sometimes', 'array'],
            'quick_links_translations.about' => ['sometimes', 'array'],
            'quick_links_translations.destinations' => ['sometimes', 'array'],
            'quick_links_translations.tours' => ['sometimes', 'array'],
            'quick_links_translations.contact' => ['sometimes', 'array'],
            'social_links' => ['sometimes', 'array'],
        ];
    }
}
