<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTPS hardening headers.
 *
 * - Strict-Transport-Security (HSTS): once a browser has loaded the site over
 *   HTTPS, force every later visit to HTTPS. Kills the insecure http-first hop
 *   (browsers type "startupkatt.com" -> http -> 301) and makes a valid-cert
 *   page's secure state sticky.
 * - Content-Security-Policy: upgrade-insecure-requests: silently upgrade any
 *   stray http:// subresource (a future comic image, an embed) to https so it
 *   can never trip Chrome's "not secure" / mixed-content warning. This is only
 *   the upgrade directive, so it never *blocks* a resource.
 *
 * Emitted only over a secure connection (or in production, where nginx
 * terminates TLS and forwards only https requests). Never sent over plain
 * http, so local `http://localhost` dev and the browser's localhost cache are
 * never poisoned with HSTS.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->secure() || app()->isProduction()) {
            // One year. No includeSubDomains / preload yet: both are one-way
            // commitments that also bind every *.startupkatt.com subdomain, so
            // add them only once all subdomains are confirmed HTTPS-only.
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');

            $response->headers->set('Content-Security-Policy', 'upgrade-insecure-requests');
        }

        return $response;
    }
}
