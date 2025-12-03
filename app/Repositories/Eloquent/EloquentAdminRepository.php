<?php

namespace App\Repositories\Eloquent;

use App\Models\Admin;
use App\Models\AdminAccessToken;
use App\Repositories\Contracts\AdminRepositoryContract;
use DateTimeInterface;

class EloquentAdminRepository implements AdminRepositoryContract
{
    public function findByEmail(string $email): ?Admin
    {
        return Admin::query()->where('email', $email)->first();
    }

    public function updateLoginMetadata(Admin $admin, ?string $ipAddress = null): void
    {
        $admin->forceFill([
            'last_login_at' => now(),
            'last_login_ip' => $ipAddress,
        ])->save();
    }

    public function createAccessToken(
        Admin $admin,
        string $plainTextToken,
        ?string $name = null,
        array $abilities = ['*'],
        ?DateTimeInterface $expiresAt = null
    ): AdminAccessToken {
        return $admin->tokens()->create([
            'name' => $name,
            'token' => hash('sha256', $plainTextToken),
            'abilities' => $abilities,
            'last_used_at' => now(),
            'expires_at' => $expiresAt,
        ]);
    }

    public function findToken(string $plainTextToken): ?AdminAccessToken
    {
        if (empty($plainTextToken)) {
            return null;
        }

        $hashed = hash('sha256', $plainTextToken);

        $token = AdminAccessToken::query()->with('admin')->where('token', $hashed)->first();

        if ($token && $token->expires_at && $token->expires_at->isPast()) {
            $token->delete();

            return null;
        }

        if ($token) {
            $token->forceFill(['last_used_at' => now()])->save();
        }

        return $token;
    }

    public function deleteToken(AdminAccessToken $token): void
    {
        $token->delete();
    }
}
