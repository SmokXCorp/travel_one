<?php

namespace App\Repositories\Eloquent;

use App\Models\AboutSection;
use App\Repositories\Contracts\AboutSectionRepositoryContract;

class EloquentAboutSectionRepository implements AboutSectionRepositoryContract
{
    public function getActive(): ?AboutSection
    {
        return AboutSection::query()->where('is_active', true)->latest('updated_at')->first();
    }

    public function getLatest(): ?AboutSection
    {
        return AboutSection::query()->latest('updated_at')->first();
    }

    public function updateOrCreate(array $data): AboutSection
    {
        $about = AboutSection::query()->first();

        if ($about) {
            $about->fill($data)->save();

            return $about->refresh();
        }

        return AboutSection::query()->create($data);
    }
}
