<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        abort_unless(
            $request->user()?->role === $role,
            Response::HTTP_FORBIDDEN,
            'Anda tidak memiliki akses ke halaman ini.',
        );

        return $next($request);
    }
}
