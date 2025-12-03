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

    public function getPublic(): ?HeroSection
    {
        return $this->heroSections->getActive() ?? $this->heroSections->getLatest();
    }

    public function getAdmin(): ?HeroSection
    {
        return $this->heroSections->getLatest();
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

        return $this->heroSections->updateOrCreate($data);
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
