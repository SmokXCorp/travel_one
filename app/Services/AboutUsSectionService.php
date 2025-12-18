<?php

namespace App\Services;

use App\Models\AboutUsSection;
use App\Repositories\Contracts\AboutUsSectionRepositoryContract;

class AboutUsSectionService
{
    public function __construct(private readonly AboutUsSectionRepositoryContract $aboutUs)
    {
    }

    public function getPublic(?string $lang = null): ?AboutUsSection
    {
        $about = $this->aboutUs->getActive() ?? $this->aboutUs->getLatest();

        if ($about && $lang) {
            $this->applyTranslations($about, $lang);
        }

        return $about;
    }

    public function getAdmin(?string $lang = null): ?AboutUsSection
    {
        $about = $this->aboutUs->getLatest();

        if ($about && $lang) {
            $this->applyTranslations($about, $lang);
        }

        return $about;
    }

    public function update(array $data, ?int $adminId = null): AboutUsSection
    {
        $current = $this->aboutUs->getLatest();

        $payload = $data;
        $payload['is_active'] = $payload['is_active'] ?? true;
        $payload['updated_by_admin_id'] = $adminId;

        $payload['title_translations'] = $data['title_translations'] ?? $current?->title_translations ?? [];
        $payload['paragraph_one_translations'] = $data['paragraph_one_translations'] ?? $current?->paragraph_one_translations ?? [];
        $payload['paragraph_two_translations'] = $data['paragraph_two_translations'] ?? $current?->paragraph_two_translations ?? [];
        $payload['paragraph_three_translations'] = $data['paragraph_three_translations'] ?? $current?->paragraph_three_translations ?? [];

        $payload['title'] = $payload['title_translations']['en'] ?? $data['title'] ?? $current?->title;
        $payload['paragraph_one'] = $payload['paragraph_one_translations']['en'] ?? $data['paragraph_one'] ?? $current?->paragraph_one;
        $payload['paragraph_two'] = $payload['paragraph_two_translations']['en'] ?? $data['paragraph_two'] ?? $current?->paragraph_two;
        $payload['paragraph_three'] = $payload['paragraph_three_translations']['en'] ?? $data['paragraph_three'] ?? $current?->paragraph_three;

        return $this->aboutUs->updateOrCreate($payload);
    }

    private function applyTranslations(AboutUsSection $about, string $lang): void
    {
        $about->title = $about->title_translations[$lang] ?? $about->title;
        $about->paragraph_one = $about->paragraph_one_translations[$lang] ?? $about->paragraph_one;
        $about->paragraph_two = $about->paragraph_two_translations[$lang] ?? $about->paragraph_two;
        $about->paragraph_three = $about->paragraph_three_translations[$lang] ?? $about->paragraph_three;
    }
}
