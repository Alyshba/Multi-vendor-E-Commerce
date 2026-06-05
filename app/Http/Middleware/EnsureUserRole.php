<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user() ?? Auth::guard('api')->user();

        if (! $user || ! in_array($user->role, $roles, true)) {
            abort(403);
        }

        return $next($request);
    }
}
