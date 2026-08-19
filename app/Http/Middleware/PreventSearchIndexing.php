<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventSearchIndexing
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // robots.txt only tells crawlers not to crawl — it doesn't stop a
        // page from being indexed if it's linked from elsewhere. This header
        // is what actually guarantees nothing on the site ends up in search
        // results, and it applies to every response, not just HTML pages.
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

        return $response;
    }
}
