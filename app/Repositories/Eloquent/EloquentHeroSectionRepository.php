<?php

namespace App\Repositories\Eloquent;

use App\Models\HeroSection;
use App\Repositories\Contracts\HeroSectionRepositoryContract;

class EloquentHeroSectionRepository implements HeroSectionRepositoryContract
{
    public function getActive(): ?HeroSection
    {
        return HeroSection::query()->where('is_active', true)->latest('updated_at')->first();
    }

    public function getLatest(): ?HeroSection
    {
        return HeroSection::query()->latest('updated_at')->first();
    }

    public function updateOrCreate(array $data): HeroSection
    {
        $hero = HeroSection::query()->first();

        if ($hero) {
            $hero->fill($data)->save();

            return $hero->refresh();
        }

        return HeroSection::query()->create($data);
    }
}
