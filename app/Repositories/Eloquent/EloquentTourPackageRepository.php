<?php

namespace App\Repositories\Eloquent;

use App\Models\TourImage;
use App\Models\TourPackage;
use App\Repositories\Contracts\TourPackageRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class EloquentTourPackageRepository implements TourPackageRepositoryContract
{
    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = TourPackage::query()->with('images');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($builder) use ($search) {
                $builder->where('title', 'like', "%{$search}%")
                    ->orWhere('short_description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['is_featured'])) {
            $query->where('is_featured', (bool) $filters['is_featured']);
        }

        return $query
            ->orderBy('display_order')
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    public function listPublic(int $limit = 0): Collection
    {
        $query = TourPackage::query()
            ->with(['images' => fn ($relation) => $relation->orderBy('sort_order')])
            ->where('is_active', true)
            ->orderBy('display_order')
            ->orderByDesc('created_at');

        if ($limit > 0) {
            $query->limit($limit);
        }

        return $query->get();
    }

    public function findById(int $id): ?TourPackage
    {
        return TourPackage::query()->with('images')->find($id);
    }

    public function findBySlug(string $slug): ?TourPackage
    {
        return TourPackage::query()
            ->with(['images' => fn ($relation) => $relation->orderBy('sort_order')])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    public function create(array $data): TourPackage
    {
        $data['slug'] = $this->generateSlug($data);

        return TourPackage::query()->create($data);
    }

    public function update(TourPackage $tourPackage, array $data): TourPackage
    {
        if (!empty($data['title']) || !empty($data['slug'])) {
            $data['slug'] = $this->generateSlug($data, $tourPackage->id);
        }

        $tourPackage->fill($data);
        $tourPackage->save();

        return $tourPackage->refresh();
    }

    public function delete(TourPackage $tourPackage): void
    {
        $tourPackage->delete();
    }

    public function addImages(TourPackage $tourPackage, array $images): void
    {
        $tourPackage->images()->createMany($images);
    }

    public function removeImages(TourPackage $tourPackage, array $imageIds): Collection
    {
        $images = $tourPackage->images()->whereIn('id', $imageIds)->get();

        TourImage::query()->whereIn('id', $images->pluck('id'))->delete();

        return $images;
    }

    public function markPrimaryImage(TourPackage $tourPackage, ?string $path): void
    {
        $tourPackage->forceFill(['primary_image_path' => $path])->save();

        $tourPackage->images()->update(['is_primary' => false]);

        if ($path) {
            $tourPackage->images()->where('path', $path)->update(['is_primary' => true]);
        }
    }

    protected function generateSlug(array $data, ?int $ignoreId = null): string
    {
        $base = Str::slug($data['slug'] ?? $data['title'] ?? Str::random(6));
        if (!$base) {
            $base = Str::slug(Str::random(6));
        }
        $slug = $base;
        $counter = 1;

        while (
            TourPackage::query()
                ->when($ignoreId, fn ($query) => $query->where('id', '<>', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = sprintf('%s-%s', $base, ++$counter);
        }

        return $slug;
    }
}
