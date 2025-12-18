<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTourPackageRequest extends FormRequest
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
            'title' => ['sometimes', 'string', 'max:255'],
            'subtitle' => ['sometimes', 'nullable', 'string', 'max:255'],
            'slug' => ['sometimes', 'nullable', 'string', 'max:255'],
            'short_description' => ['sometimes', 'nullable', 'string'],
            'description' => ['sometimes', 'nullable', 'string'],
            'title_translations' => ['sometimes', 'array'],
            'title_translations.*' => ['nullable', 'string', 'max:255'],
            'subtitle_translations' => ['sometimes', 'array'],
            'subtitle_translations.*' => ['nullable', 'string', 'max:255'],
            'short_description_translations' => ['sometimes', 'array'],
            'short_description_translations.*' => ['nullable', 'string'],
            'description_translations' => ['sometimes', 'array'],
            'description_translations.*' => ['nullable', 'string'],
            'location_translations' => ['sometimes', 'array'],
            'location_translations.*' => ['nullable', 'string', 'max:255'],
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'duration' => ['sometimes', 'nullable', 'string', 'max:255'],
            'location' => ['sometimes', 'nullable', 'string', 'max:255'],
            'is_active' => ['sometimes', 'boolean'],
            'is_featured' => ['sometimes', 'boolean'],
            'display_order' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'images' => ['sometimes', 'array'],
            'images.*' => ['file', 'image', 'max:5120'],
            'remove_image_ids' => ['sometimes', 'array'],
            'remove_image_ids.*' => ['integer', 'exists:tour_images,id'],
            'primary_image_id' => ['sometimes', 'nullable', 'integer', 'exists:tour_images,id'],
            'primary_image_path' => ['sometimes', 'nullable', 'string', 'max:255'],
        ];
    }
}
