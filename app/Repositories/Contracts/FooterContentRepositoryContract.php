<?php

namespace App\Repositories\Contracts;

use App\Models\FooterContent;

interface FooterContentRepositoryContract
{
    public function getActive(): ?FooterContent;

    public function getLatest(): ?FooterContent;

    public function updateOrCreate(array $data): FooterContent;
}
