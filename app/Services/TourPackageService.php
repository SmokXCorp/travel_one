<?php

namespace App\Services;

use App\Models\TourPackage;
use App\Repositories\Contracts\TourPackageRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class TourPackageService
{
    public function __construct(private readonly TourPackageRepositoryContract $tourPackages)
    {
    }

    public function paginateForAdmin(array $filters = [], int $perPage = 15, ?string $lang = null): LengthAwarePaginator
    {
        $paginator = $this->tourPackages->paginateForAdmin($filters, $perPage);

        if ($lang) {
            $paginator->getCollection()->transform(function (TourPackage $tour) use ($lang) {
                $this->applyTranslations($tour, $lang);
                return $tour;
            });
        }

        return $paginator;
    }

    public function listPublic(int $limit = 0, ?string $lang = null): EloquentCollection
    {
        $tours = $this->tourPackages->listPublic($limit);

        if ($lang) {
            $tours->each(fn (TourPackage $tour) => $this->applyTranslations($tour, $lang));
        }

        return $tours;
    }

    public function findForAdmin(int $id, ?string $lang = null): ?TourPackage
    {
        $tour = $this->tourPackages->findById($id);

        if ($tour && $lang) {
            $this->applyTranslations($tour, $lang);
        }

        return $tour;
    }

    public function findPublic(string $slug, ?string $lang = null): ?TourPackage
    {
        $tour = $this->tourPackages->findBySlug($slug);

        if ($tour && $lang) {
            $this->applyTranslations($tour, $lang);
        }

        return $tour;
    }

    public function create(array $data, array $imageFiles = []): TourPackage
    {
        $data = $this->mergeTranslationDefaultsForPayload($data);
        $imagePayloads = $this->storeImages($imageFiles);
        $data['primary_image_path'] = $data['primary_image_path'] ?? ($imagePayloads[0]['path'] ?? null);

        $tour = $this->tourPackages->create($data);

        if (!empty($imagePayloads)) {
            $this->flagPrimaryOnPayload($imagePayloads, $data['primary_image_path']);
            $this->tourPackages->addImages($tour, $imagePayloads);
        }

        $this->tourPackages->markPrimaryImage($tour, $data['primary_image_path'] ?? null);

        return $this->tourPackages->findById($tour->id);
    }

    public function update(
        TourPackage $tour,
        array $data,
        array $imageFiles = [],
        array $imagesToRemove = []
    ): TourPackage {
        $data = $this->mergeTranslationDefaultsForPayload($data, $tour);
        $primaryPath = $tour->primary_image_path;

        if (array_key_exists('primary_image_id', $data)) {
            $primaryId = $data['primary_image_id'];
            unset($data['primary_image_id']);
            $primaryPath = $primaryId
                ? $tour->images()->where('id', $primaryId)->value('path')
                : null;
        }

        if (array_key_exists('primary_image_path', $data)) {
            $primaryPath = $data['primary_image_path'];
        }
        unset($data['primary_image_path']);

        if (!empty($imagesToRemove)) {
            $removed = $this->tourPackages->removeImages($tour, $imagesToRemove);
            $removedPaths = $removed->pluck('path');
            $this->deleteStoredImages($removedPaths->all());

            if ($primaryPath && $removedPaths->contains($primaryPath)) {
                $primaryPath = null;
            }
        }

        $newImages = $this->storeImages($imageFiles, $tour);

        if (!empty($newImages)) {
            if (!$primaryPath) {
                $primaryPath = $newImages[0]['path'];
            }

            $this->flagPrimaryOnPayload($newImages, $primaryPath);
            $this->tourPackages->addImages($tour, $newImages);
        }

        $updated = $this->tourPackages->update($tour, $data);

        if (!$primaryPath) {
            $primaryPath = $this->resolvePrimaryImagePath($updated);
        }

        $this->tourPackages->markPrimaryImage($updated, $primaryPath);

        return $this->tourPackages->findById($updated->id);
    }

    public function delete(TourPackage $tour): void
    {
        $paths = $tour->images()->pluck('path')->all();
        $this->tourPackages->delete($tour);
        $this->deleteStoredImages($paths);

        if ($tour->primary_image_path && !in_array($tour->primary_image_path, $paths, true)) {
            $this->deleteStoredImages([$tour->primary_image_path]);
        }
    }

    private function storeImages(?array $files, ?TourPackage $tour = null): array
    {
        if (empty($files)) {
            return [];
        }

        $payloads = [];
        $sortOrder = $tour ? (int) ($tour->images()->max('sort_order') ?? 0) + 1 : 0;

        foreach ($files as $file) {
            if (!$file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('tours', 'public');

            $payloads[] = [
                'path' => $path,
                'caption' => null,
                'alt_text' => null,
                'is_primary' => false,
                'sort_order' => $sortOrder++,
            ];
        }

        return $payloads;
    }

    private function flagPrimaryOnPayload(array &$payloads, ?string $primaryPath): void
    {
        if (!$primaryPath) {
            return;
        }

        foreach ($payloads as &$payload) {
            $payload['is_primary'] = $payload['path'] === $primaryPath;
        }
    }

    private function resolvePrimaryImagePath(TourPackage $tour): ?string
    {
        $tour->loadMissing(['images' => fn ($relation) => $relation->orderBy('sort_order')]);

        if ($tour->primary_image_path && $tour->images->contains('path', $tour->primary_image_path)) {
            return $tour->primary_image_path;
        }

        return $tour->images->sortBy('sort_order')->first()?->path;
    }

    private function deleteStoredImages(array $paths): void
    {
        if (empty($paths)) {
            return;
        }

        foreach ($paths as $path) {
            if ($path) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function mergeTranslationDefaultsForPayload(array $data, ?TourPackage $current = null): array
    {
        $data['title_translations'] = $this->mergeTranslationDefaults(
            $data['title_translations'] ?? ($current?->title_translations ?? []),
            $data['title'] ?? $current?->title
        );
        $data['subtitle_translations'] = $this->mergeTranslationDefaults(
            $data['subtitle_translations'] ?? ($current?->subtitle_translations ?? []),
            $data['subtitle'] ?? $current?->subtitle
        );
        $data['short_description_translations'] = $this->mergeTranslationDefaults(
            $data['short_description_translations'] ?? ($current?->short_description_translations ?? []),
            $data['short_description'] ?? $current?->short_description
        );
        $data['description_translations'] = $this->mergeTranslationDefaults(
            $data['description_translations'] ?? ($current?->description_translations ?? []),
            $data['description'] ?? $current?->description
        );
        $data['location_translations'] = $this->mergeTranslationDefaults(
            $data['location_translations'] ?? ($current?->location_translations ?? []),
            $data['location'] ?? $current?->location
        );

        $data['title'] = $data['title_translations']['en'] ?? $data['title'] ?? $current?->title;
        $data['subtitle'] = $data['subtitle_translations']['en'] ?? $data['subtitle'] ?? $current?->subtitle;
        $data['short_description'] = $data['short_description_translations']['en'] ?? $data['short_description'] ?? $current?->short_description;
        $data['description'] = $data['description_translations']['en'] ?? $data['description'] ?? $current?->description;
        $data['location'] = $data['location_translations']['en'] ?? $data['location'] ?? $current?->location;

        return $data;
    }

    private function mergeTranslationDefaults(?array $translations, ?string $fallback): array
    {
        $translations = $translations ?? [];
        if (!array_key_exists('en', $translations) && $fallback !== null) {
            $translations['en'] = $fallback;
        }

        return $translations;
    }

    private function applyTranslations(TourPackage $tour, string $lang): void
    {
        $tour->title = $tour->title_translations[$lang] ?? $tour->title;
        $tour->subtitle = $tour->subtitle_translations[$lang] ?? $tour->subtitle;
        $tour->short_description = $tour->short_description_translations[$lang] ?? $tour->short_description;
        $tour->description = $tour->description_translations[$lang] ?? $tour->description;
        $tour->location = $tour->location_translations[$lang] ?? $tour->location;
    }
}
