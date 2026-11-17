<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Shows an explanation page instead of a bare 403 when premium access is missing.
 */
class EnsureUserHasPremiumAccess
{
    /**
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Gate::denies('premium')) {
            return response()->view('premium-required', status: Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }
}
