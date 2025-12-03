<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminLoginRequest;
use App\Services\AdminAuthService;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthController extends Controller
{
    public function __construct(private readonly AdminAuthService $authService)
    {
    }

    public function login(AdminLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $result = $this->authService->login(
                $data['email'],
                $data['password'],
                $request->ip()
            );
        } catch (AuthenticationException $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], Response::HTTP_UNAUTHORIZED);
        }

        return response()->json([
            'token' => $result['token'],
            'admin' => $result['admin'],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $token = $request->bearerToken();
        $this->authService->logout($token);

        return response()->json([
            'message' => 'Logged out',
        ]);
    }
}
