<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryContract;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminAuthService
{
    public function __construct(private readonly AdminRepositoryContract $admins)
    {
    }

    /**
     * @throws AuthenticationException
     */
    public function login(string $email, string $password, ?string $ipAddress = null): array
    {
        $admin = $this->admins->findByEmail($email);

        if (!$admin || !Hash::check($password, $admin->password)) {
            throw new AuthenticationException('Invalid credentials.');
        }

        $plainToken = Str::random(64);
        $token = $this->admins->createAccessToken($admin, $plainToken);
        $this->admins->updateLoginMetadata($admin, $ipAddress);

        return [
            'token' => $plainToken,
            'admin' => $admin,
            'token_model' => $token,
        ];
    }

    public function logout(?string $plainTextToken): void
    {
        $token = $this->admins->findToken($plainTextToken);

        if ($token) {
            $this->admins->deleteToken($token);
        }
    }

    public function resolveAdminFromToken(?string $plainTextToken): ?Admin
    {
        $token = $this->admins->findToken($plainTextToken);

        return $token?->admin;
    }
}
