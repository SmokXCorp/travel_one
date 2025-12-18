<?php

namespace App\Repositories\Contracts;

use App\Models\AboutUsSection;

interface AboutUsSectionRepositoryContract
{
    public function getActive(): ?AboutUsSection;

    public function getLatest(): ?AboutUsSection;

    public function updateOrCreate(array $data): AboutUsSection;
}
