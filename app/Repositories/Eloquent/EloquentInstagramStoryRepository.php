<?php

namespace App\Repositories\Eloquent;

use App\Models\InstagramStory;
use App\Repositories\Contracts\InstagramStoryRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EloquentInstagramStoryRepository implements InstagramStoryRepositoryContract
{
    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = InstagramStory::query();

        if (isset($filters['is_active'])) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        return $query->orderBy('sort_order')->orderByDesc('created_at')->paginate($perPage);
    }

    public function listActive(): Collection
    {
        return InstagramStory::query()
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderByDesc('created_at')
            ->get();
    }

    public function findById(int $id): ?InstagramStory
    {
        return InstagramStory::query()->find($id);
    }

    public function create(array $data): InstagramStory
    {
        return InstagramStory::query()->create($data);
    }

    public function update(InstagramStory $story, array $data): InstagramStory
    {
        $story->fill($data)->save();

        return $story->refresh();
    }

    public function delete(InstagramStory $story): void
    {
        $story->delete();
    }
}
