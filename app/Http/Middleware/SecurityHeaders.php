<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $isLocal = app()->environment('local');

        $directives = [
            "default-src 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "frame-ancestors 'none'",
            "form-action 'self'",
            "script-src 'self' 'unsafe-inline'".($isLocal ? ' http://localhost:* http://127.0.0.1:*' : '')." https://www.smartsuppchat.com https://*.smartsuppchat.com https://*.smartsupp.com https://*.smartsuppcdn.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://*.smartsuppcdn.com".($isLocal ? ' http://localhost:* http://127.0.0.1:*' : ''),
            "font-src 'self' data: https://fonts.gstatic.com https://*.smartsuppcdn.com",
            "img-src 'self' data: blob: https:",
            "connect-src 'self'".($isLocal ? ' http://localhost:* http://127.0.0.1:* ws://localhost:* ws://127.0.0.1:*' : '')." https://*.smartsupp.com https://*.smartsuppchat.com https://*.smartsuppcdn.com wss://*.smartsupp.com wss://*.smartsuppchat.com",
            "frame-src https://*.smartsupp.com https://*.smartsuppchat.com https://*.smartsuppcdn.com",
        ];

        if (! $isLocal) {
            $directives[] = 'upgrade-insecure-requests';
        }

        $csp = implode('; ', $directives);

        $response->headers->set('Content-Security-Policy', $csp);
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set(
            'Permissions-Policy',
            'camera=(), geolocation=(), microphone=(), payment=(), usb=()'
        );
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        $response->headers->set(
            'Strict-Transport-Security',
            'max-age=31536000; includeSubDomains'
        );

        $response->headers->remove('X-Powered-By');

        return $response;
    }
}