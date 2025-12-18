<?php

namespace App\Repositories\Eloquent;

use App\Models\AboutUsSection;
use App\Repositories\Contracts\AboutUsSectionRepositoryContract;

class EloquentAboutUsSectionRepository implements AboutUsSectionRepositoryContract
{
    public function getActive(): ?AboutUsSection
    {
        return AboutUsSection::query()->where('is_active', true)->latest('updated_at')->first();
    }

    public function getLatest(): ?AboutUsSection
    {
        return AboutUsSection::query()->latest('updated_at')->first();
    }

    public function updateOrCreate(array $data): AboutUsSection
    {
        $about = AboutUsSection::query()->first();

        if ($about) {
            $about->fill($data)->save();
            return $about->refresh();
        }

        return AboutUsSection::query()->create($data);
    }
}
