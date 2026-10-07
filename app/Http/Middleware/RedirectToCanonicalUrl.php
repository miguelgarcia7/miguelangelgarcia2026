<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Keeps the site on one address in production: https plus the host in
 * APP_URL. http://, www. and any other alias are sent there in a single
 * redirect, so visitors and crawlers only ever see one copy of the site.
 * Local (Herd) and test requests are left alone.
 */
class RedirectToCanonicalUrl
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = parse_url(config('app.url'), PHP_URL_HOST);

        if (! app()->isProduction() || ! $host || ($request->secure() && $request->getHost() === $host)) {
            return $next($request);
        }

        // 308 keeps the method and body for anything that isn't a plain read.
        $status = $request->isMethodSafe() ? 301 : 308;

        return redirect()->away('https://'.$host.$request->getRequestUri(), $status);
    }
}
