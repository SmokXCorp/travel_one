<?php

namespace App\Repositories\Eloquent;

use App\Models\FooterContent;
use App\Repositories\Contracts\FooterContentRepositoryContract;

class EloquentFooterContentRepository implements FooterContentRepositoryContract
{
    public function getActive(): ?FooterContent
    {
        return FooterContent::query()->where('is_active', true)->latest('updated_at')->first();
    }

    public function getLatest(): ?FooterContent
    {
        return FooterContent::query()->latest('updated_at')->first();
    }

    public function updateOrCreate(array $data): FooterContent
    {
        $footer = FooterContent::query()->first();

        if ($footer) {
            $footer->fill($data)->save();
            return $footer->refresh();
        }

        return FooterContent::query()->create($data);
    }
}
