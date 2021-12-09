<?php

namespace App\Http\Middleware;

use App\Service\AuthService;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class AccessToApi
{
    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  Request  $request
     * @return string|null
     */
    public function handle($request, Closure $next)
    {
        $user = $this->authService->getUser();
        if (!$request->expectsJson() && !$user) {
            return response(['message' => "В доступе отказано."], 403);
        }

        if (!$user->isAdmin()) {
            $endedAt = Carbon::make($user->end_access_to_api_at);
            if (!$user->is_has_access_to_api || Carbon::now()->greaterThan($endedAt)) {
                return response()->json(['message' => "В доступе отказано."], 403);
            }
        }

        return $next($request);
    }
}
