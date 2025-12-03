<?php

namespace App\Repositories\Contracts;

use App\Models\HeroSection;

interface HeroSectionRepositoryContract
{
    public function getActive(): ?HeroSection;

    public function getLatest(): ?HeroSection;

    public function updateOrCreate(array $data): HeroSection;
}
