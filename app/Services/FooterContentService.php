<?php

namespace App\Services;

use App\Models\FooterContent;
use App\Repositories\Contracts\FooterContentRepositoryContract;

class FooterContentService
{
    public function __construct(private readonly FooterContentRepositoryContract $footers)
    {
    }

    public function getPublic(?string $lang = null): ?FooterContent
    {
        $footer = $this->footers->getActive() ?? $this->footers->getLatest();

        if ($footer && $lang) {
            $this->applyTranslations($footer, $lang);
        }

        return $footer;
    }

    public function getAdmin(?string $lang = null): ?FooterContent
    {
        $footer = $this->footers->getLatest();

        if ($footer && $lang) {
            $this->applyTranslations($footer, $lang);
        }

        return $footer;
    }

    public function update(array $data, ?int $adminId = null): FooterContent
    {
        $current = $this->footers->getLatest();

        $payload = $data;
        $payload['is_active'] = $payload['is_active'] ?? true;
        $payload['updated_by_admin_id'] = $adminId;

        $payload['quick_links_translations'] = $data['quick_links_translations'] ?? $current?->quick_links_translations ?? [];
        $payload['social_links'] = $data['social_links'] ?? $current?->social_links ?? [];

        return $this->footers->updateOrCreate($payload);
    }

    private function applyTranslations(FooterContent $footer, string $lang): void
    {
        $links = $footer->quick_links_translations ?? [];
        foreach (['about', 'destinations', 'tours', 'contact'] as $key) {
            $footer->setAttribute($key . '_label', $links[$key][$lang] ?? $links[$key]['en'] ?? $key);
        }
    }
}
