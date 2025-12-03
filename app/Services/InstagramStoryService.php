<?php

namespace App\Services;

use App\Models\InstagramStory;
use App\Repositories\Contracts\InstagramStoryRepositoryContract;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class InstagramStoryService
{
    public function __construct(private readonly InstagramStoryRepositoryContract $stories)
    {
    }

    public function paginateForAdmin(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->stories->paginateForAdmin($filters, $perPage);
    }

    public function listActive(): Collection
    {
        return $this->stories->listActive();
    }

    public function find(int $id): ?InstagramStory
    {
        return $this->stories->findById($id);
    }

    public function create(array $data, ?UploadedFile $image = null): InstagramStory
    {
        if ($image) {
            $data['image_path'] = $image->store('instagram', 'public');
        }

        return $this->stories->create($data);
    }

    public function update(InstagramStory $story, array $data, ?UploadedFile $image = null): InstagramStory
    {
        if ($image) {
            $this->deleteImage($story->image_path);
            $data['image_path'] = $image->store('instagram', 'public');
        }

        return $this->stories->update($story, $data);
    }

    public function delete(InstagramStory $story): void
    {
        $this->stories->delete($story);
        $this->deleteImage($story->image_path);
    }

    private function deleteImage(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
