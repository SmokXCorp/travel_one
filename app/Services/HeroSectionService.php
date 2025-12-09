<?php

namespace App\Services;

use App\Models\HeroSection;
use App\Repositories\Contracts\HeroSectionRepositoryContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class HeroSectionService
{
    public function __construct(private readonly HeroSectionRepositoryContract $heroSections)
    {
    }

    public function getPublic(?string $lang = null): ?HeroSection
    {
        $hero = $this->heroSections->getActive() ?? $this->heroSections->getLatest();

        if ($hero && $lang) {
            $this->applyTranslations($hero, $lang);
        }

        return $hero;
    }

    public function getAdmin(?string $lang = null): ?HeroSection
    {
        $hero = $this->heroSections->getLatest();

        if ($hero && $lang) {
            $this->applyTranslations($hero, $lang);
        }

        return $hero;
    }

    public function update(array $data, ?UploadedFile $image = null, ?int $adminId = null): HeroSection
    {
        $current = $this->heroSections->getLatest();

        if ($image) {
            if ($current) {
                $this->deleteImage($current->image_path);
            }

            $data['image_path'] = $image->store('hero', 'public');
        }

        $data['is_active'] = $data['is_active'] ?? true;
        $data['updated_by_admin_id'] = $adminId;

        $data['headline_translations'] = $data['headline_translations'] ?? $current?->headline_translations ?? [];
        $data['subheadline_translations'] = $data['subheadline_translations'] ?? $current?->subheadline_translations ?? [];
        $data['description_translations'] = $data['description_translations'] ?? $current?->description_translations ?? [];
        $data['button_text_translations'] = $data['button_text_translations'] ?? $current?->button_text_translations ?? [];

        $data['headline'] = $data['headline_translations']['en'] ?? $data['headline'] ?? $current?->headline;
        $data['subheadline'] = $data['subheadline_translations']['en'] ?? $data['subheadline'] ?? $current?->subheadline;
        $data['description'] = $data['description_translations']['en'] ?? $data['description'] ?? $current?->description;
        $data['button_text'] = $data['button_text_translations']['en'] ?? $data['button_text'] ?? $current?->button_text;

        return $this->heroSections->updateOrCreate($data);
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function applyTranslations(HeroSection $hero, string $lang): void
    {
        $hero->headline = $hero->headline_translations[$lang] ?? $hero->headline;
        $hero->subheadline = $hero->subheadline_translations[$lang] ?? $hero->subheadline;
        $hero->description = $hero->description_translations[$lang] ?? $hero->description;
        $hero->button_text = $hero->button_text_translations[$lang] ?? $hero->button_text;
    }
}
