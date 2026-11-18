<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uses the language the visitor chose: from their account when logged in, otherwise from the session.
 * Without a choice the site default (APP_LOCALE) applies.
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->user()?->locale ?? $request->session()->get('locale');

        if (is_string($locale) && array_key_exists($locale, config('cachetrail.locales'))) {
            app()->setLocale($locale);
        }

        return $next($request);
    }
}
