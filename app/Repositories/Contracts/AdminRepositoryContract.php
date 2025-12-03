<?php

namespace App\Repositories\Contracts;

use App\Models\Admin;
use App\Models\AdminAccessToken;
use DateTimeInterface;

interface AdminRepositoryContract
{
    public function findByEmail(string $email): ?Admin;

    public function updateLoginMetadata(Admin $admin, ?string $ipAddress = null): void;

    public function createAccessToken(
        Admin $admin,
        string $plainTextToken,
        ?string $name = null,
        array $abilities = ['*'],
        ?DateTimeInterface $expiresAt = null
    ): AdminAccessToken;

    public function findToken(string $plainTextToken): ?AdminAccessToken;

    public function deleteToken(AdminAccessToken $token): void;
}
