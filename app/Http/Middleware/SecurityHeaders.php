<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * HTTPS hardening header.
 *
 * Content-Security-Policy: upgrade-insecure-requests: silently upgrade any
 * stray http:// subresource (a future comic image, an embed) to https so it
 * can never trip Chrome's "not secure" / mixed-content warning. This is only
 * the upgrade directive, so it never *blocks* a resource.
 *
 * NB: HSTS (Strict-Transport-Security) was deliberately removed. Singtel
 * intercepts this specific hostname's TLS with a self-signed CN=singtel.com
 * cert (an ISP-level, SNI-based block of the domain, not a server/cert fault:
 * sibling sites on the same box are untouched, and CT logs show only real
 * Let's Encrypt certs). HSTS turned that interception into an *unbypassable*
 * hard block for every Singtel visitor. Without HSTS it degrades to a
 * bypassable warning. Re-add HSTS (and consider preload) once Singtel stops
 * flagging the domain. See memory: singtel-block-ssl-cert-chain.
 *
 * Emitted only over a secure connection (or in production, where nginx
 * terminates TLS and forwards only https requests). Never sent over plain
 * http, so local `http://localhost` dev isn't affected.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->secure() || app()->isProduction()) {
            $response->headers->set('Content-Security-Policy', 'upgrade-insecure-requests');
        }

        return $response;
    }
}
