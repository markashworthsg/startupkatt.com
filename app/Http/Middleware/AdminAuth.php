<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * A single HTTP Basic gate for the /admin area.
 *
 * No users table, no sessions — just the ADMIN_USERNAME / ADMIN_PASSWORD pair
 * from config. If no password is configured the area is locked entirely so it
 * is never accidentally left open.
 */
class AdminAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $username = (string) config('comics.admin.username');
        $password = (string) config('comics.admin.password');

        // Locked until a password is configured.
        if ($password === '') {
            abort(Response::HTTP_FORBIDDEN, 'Admin is locked. Set ADMIN_PASSWORD in .env to enable it.');
        }

        $givenUser = (string) $request->getUser();
        $givenPass = (string) $request->getPassword();

        // hash_equals on both fields, timing-safe, order-independent.
        $ok = hash_equals($username, $givenUser) & hash_equals($password, $givenPass);

        if (! $ok) {
            return response('Authentication required.', Response::HTTP_UNAUTHORIZED, [
                'WWW-Authenticate' => 'Basic realm="Startup Katt admin"',
            ]);
        }

        return $next($request);
    }
}
