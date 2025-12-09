<?php

namespace App\Services;

use App\Models\AboutSection;
use App\Repositories\Contracts\AboutSectionRepositoryContract;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class AboutSectionService
{
    public function __construct(private readonly AboutSectionRepositoryContract $aboutSections)
    {
    }

    public function getPublic(?string $lang = null): ?AboutSection
    {
        $about = $this->aboutSections->getActive() ?? $this->aboutSections->getLatest();

        if ($about && $lang) {
            $this->applyTranslations($about, $lang);
        }

        return $about;
    }

    public function getAdmin(?string $lang = null): ?AboutSection
    {
        $about = $this->aboutSections->getLatest();

        if ($about && $lang) {
            $this->applyTranslations($about, $lang);
        }

        return $about;
    }

    public function update(array $data, array $images = [], ?int $adminId = null): AboutSection
    {
        $current = $this->aboutSections->getLatest();

        $payload = $data;
        $payload['is_active'] = $payload['is_active'] ?? true;
        $payload['updated_by_admin_id'] = $adminId;

        $payload['title_translations'] = $data['title_translations'] ?? $current?->title_translations ?? [];
        $payload['description_primary_translations'] = $data['description_primary_translations'] ?? $current?->description_primary_translations ?? [];
        $payload['description_secondary_translations'] = $data['description_secondary_translations'] ?? $current?->description_secondary_translations ?? [];

        $payload['title'] = $payload['title_translations']['en'] ?? $data['title'] ?? $current?->title;
        $payload['description_primary'] = $payload['description_primary_translations']['en'] ?? $data['description_primary'] ?? $current?->description_primary;
        $payload['description_secondary'] = $payload['description_secondary_translations']['en'] ?? $data['description_secondary'] ?? $current?->description_secondary;

        $imageKeys = [
            'image_one' => 'image_one_path',
            'image_two' => 'image_two_path',
            'image_three' => 'image_three_path',
        ];

        foreach ($imageKeys as $inputKey => $column) {
            /** @var UploadedFile|null $file */
            $file = $images[$inputKey] ?? null;
            if ($file) {
                if ($current && $current->$column) {
                    $this->deleteImage($current->$column);
                }

                $payload[$column] = $file->store('about', 'public');
            }
        }

        return $this->aboutSections->updateOrCreate($payload);
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }

    private function applyTranslations(AboutSection $about, string $lang): void
    {
        $about->title = $about->title_translations[$lang] ?? $about->title;
        $about->description_primary = $about->description_primary_translations[$lang] ?? $about->description_primary;
        $about->description_secondary = $about->description_secondary_translations[$lang] ?? $about->description_secondary;
    }
}
