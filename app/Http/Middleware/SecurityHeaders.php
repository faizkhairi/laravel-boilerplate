<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Routes where a strict Content-Security-Policy is skipped.
     *
     * Scramble's bundled API docs UI (Stoplight Elements) loads a script and
     * stylesheet from unpkg.com and renders several inline <script> blocks
     * that come from the dedoc/scramble package view, not this app, so they
     * cannot be given our nonce. Rewriting a vendor view would break on the
     * next `composer update`, so the strict policy is scoped to app-owned
     * pages and skipped only for this internal, auth-agnostic docs route.
     * The other headers below (frame options, nosniff, etc.) still apply.
     *
     * @var list<string>
     */
    private const CSP_EXEMPT_ROUTES = [
        'docs/api',
        'docs/api/*',
        'docs/api.json',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        Vite::useCspNonce();

        /** @var Response $response */
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if ($request->isSecure()) {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains'
            );
        }

        if (! $request->is(...self::CSP_EXEMPT_ROUTES)) {
            $response->headers->set('Content-Security-Policy', $this->buildCsp($request));
        }

        return $response;
    }

    /**
     * Build the Content-Security-Policy header value.
     */
    private function buildCsp(Request $request): string
    {
        $nonce = Vite::cspNonce();

        $scriptSrc = ["'self'", "'nonce-{$nonce}'"];
        $styleSrc = ["'self'", "'unsafe-inline'", 'https://fonts.bunny.net'];
        $fontSrc = ["'self'", 'https://fonts.bunny.net'];
        // laravel.com serves the decorative images on the stock Welcome page.
        // Drop it once you replace that page with your own.
        $imgSrc = ["'self'", 'data:', 'https://laravel.com'];
        $connectSrc = ["'self'"];

        // In local dev the Vite dev server serves assets (and HMR over a
        // websocket) from a separate origin. public/hot only exists while
        // `vite dev`/`npm run dev` is running, so this never affects a
        // built/production response.
        if ($devServerOrigin = $this->viteDevServerOrigin()) {
            $scriptSrc[] = $devServerOrigin;
            $styleSrc[] = $devServerOrigin;
            $fontSrc[] = $devServerOrigin;
            $imgSrc[] = $devServerOrigin;
            $connectSrc[] = $devServerOrigin;
            $connectSrc[] = str_replace('http', 'ws', $devServerOrigin);
        }

        $directives = [
            "default-src 'self'",
            'script-src '.implode(' ', $scriptSrc),
            'style-src '.implode(' ', $styleSrc),
            'font-src '.implode(' ', $fontSrc),
            'img-src '.implode(' ', $imgSrc),
            'connect-src '.implode(' ', $connectSrc),
            "base-uri 'self'",
            "form-action 'self'",
            "frame-ancestors 'none'",
        ];

        return implode('; ', $directives);
    }

    /**
     * Read the Vite dev server origin from the `public/hot` file, if present.
     */
    private function viteDevServerOrigin(): ?string
    {
        $hotFile = public_path('hot');

        if (! file_exists($hotFile)) {
            return null;
        }

        $contents = trim((string) file_get_contents($hotFile));

        if ($contents === '') {
            return null;
        }

        $origin = parse_url($contents, PHP_URL_SCHEME).'://'.parse_url($contents, PHP_URL_HOST);

        if ($port = parse_url($contents, PHP_URL_PORT)) {
            $origin .= ":{$port}";
        }

        return $origin;
    }
}
