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

    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->tourPackages->paginateForAdmin($filters, $perPage);
    }

    public function listPublic(int $limit = 0): EloquentCollection
    {
        return $this->tourPackages->listPublic($limit);
    }

    public function findForAdmin(int $id): ?TourPackage
    {
        return $this->tourPackages->findById($id);
    }

    public function findPublic(string $slug): ?TourPackage
    {
        return $this->tourPackages->findBySlug($slug);
    }

    public function create(array $data, array $imageFiles = []): TourPackage
    {
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
}
