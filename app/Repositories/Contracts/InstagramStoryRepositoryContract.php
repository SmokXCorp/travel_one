<?php

namespace App\Repositories\Contracts;

use App\Models\InstagramStory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface InstagramStoryRepositoryContract
{
    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator;

    public function listActive(): Collection;

    public function findById(int $id): ?InstagramStory;

    public function create(array $data): InstagramStory;

    public function update(InstagramStory $story, array $data): InstagramStory;

    public function delete(InstagramStory $story): void;
}
