<?php

namespace App\Repositories\Contracts;

use App\Models\AboutSection;

interface AboutSectionRepositoryContract
{
    public function getActive(): ?AboutSection;

    public function getLatest(): ?AboutSection;

    public function updateOrCreate(array $data): AboutSection;
}
