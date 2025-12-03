<?php

namespace App\Repositories\Contracts;

use App\Models\TourPackage;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface TourPackageRepositoryContract
{
    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function listPublic(int $limit = 0): Collection;

    public function findById(int $id): ?TourPackage;

    public function findBySlug(string $slug): ?TourPackage;

    public function create(array $data): TourPackage;

    public function update(TourPackage $tourPackage, array $data): TourPackage;

    public function delete(TourPackage $tourPackage): void;

    public function addImages(TourPackage $tourPackage, array $images): void;

    public function removeImages(TourPackage $tourPackage, array $imageIds): Collection;

    public function markPrimaryImage(TourPackage $tourPackage, ?string $path): void;
}
