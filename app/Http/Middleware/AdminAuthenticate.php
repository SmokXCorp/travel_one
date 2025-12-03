<?php

namespace App\Http\Middleware;

use App\Repositories\Contracts\AdminRepositoryContract;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminAuthenticate
{
    public function __construct(private readonly AdminRepositoryContract $admins)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $plainToken = $this->resolveToken($request);
        $token = $this->admins->findToken($plainToken);

        if (!$token || !$token->admin) {
            return $this->unauthorizedResponse();
        }

        $request->setUserResolver(fn () => $token->admin);
        auth()->setUser($token->admin);

        return $next($request);
    }

    protected function resolveToken(Request $request): ?string
    {
        return $request->bearerToken()
            ?? $request->query('api_token')
            ?? $request->query('token');
    }

    protected function unauthorizedResponse(): JsonResponse
    {
        return response()->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
    }
}
