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

    public function paginateForAdmin(array $filters = [], int $perPage = 15, ?string $lang = null): LengthAwarePaginator
    {
        $paginator = $this->stories->paginateForAdmin($filters, $perPage);

        if ($lang) {
            $paginator->getCollection()->transform(function (InstagramStory $story) use ($lang) {
                $this->applyTranslations($story, $lang);
                return $story;
            });
        }

        return $paginator;
    }

    public function listActive(?string $lang = null): Collection
    {
        $stories = $this->stories->listActive();

        if ($lang) {
            $stories->each(fn (InstagramStory $story) => $this->applyTranslations($story, $lang));
        }

        return $stories;
    }

    public function find(int $id, ?string $lang = null): ?InstagramStory
    {
        $story = $this->stories->findById($id);

        if ($story && $lang) {
            $this->applyTranslations($story, $lang);
        }

        return $story;
    }

    public function create(array $data, ?UploadedFile $image = null): InstagramStory
    {
        $data = $this->mergeTranslationDefaults($data);

        if ($image) {
            $data['image_path'] = $image->store('instagram', 'public');
        }

        return $this->stories->create($data);
    }

    public function update(InstagramStory $story, array $data, ?UploadedFile $image = null): InstagramStory
    {
        $data = $this->mergeTranslationDefaults($data, $story);

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

    private function mergeTranslationDefaults(array $data, ?InstagramStory $current = null): array
    {
        $data['title_translations'] = $data['title_translations'] ?? $current?->title_translations ?? [];
        $data['caption_translations'] = $data['caption_translations'] ?? $current?->caption_translations ?? [];

        if (!array_key_exists('en', $data['title_translations']) && ($data['title'] ?? $current?->title)) {
            $data['title_translations']['en'] = $data['title'] ?? $current?->title;
        }

        if (!array_key_exists('en', $data['caption_translations']) && ($data['caption'] ?? $current?->caption)) {
            $data['caption_translations']['en'] = $data['caption'] ?? $current?->caption;
        }

        $data['title'] = $data['title_translations']['en'] ?? $data['title'] ?? $current?->title;
        $data['caption'] = $data['caption_translations']['en'] ?? $data['caption'] ?? $current?->caption;

        return $data;
    }

    private function applyTranslations(InstagramStory $story, string $lang): void
    {
        $story->title = $story->title_translations[$lang] ?? $story->title;
        $story->caption = $story->caption_translations[$lang] ?? $story->caption;
    }
}
