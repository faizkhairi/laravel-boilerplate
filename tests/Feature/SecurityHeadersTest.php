<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    public function test_security_headers_are_present_on_the_homepage(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');
        $response->assertHeaderMissing('Strict-Transport-Security');
        $this->assertTrue($response->headers->has('Content-Security-Policy'));
    }

    public function test_security_headers_are_present_on_the_health_check(): void
    {
        $response = $this->get('/up');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
    }

    public function test_the_csp_has_a_nonce_and_no_unsafe_inline_script_src(): void
    {
        $response = $this->get('/');

        $csp = (string) $response->headers->get('Content-Security-Policy');

        $this->assertMatchesRegularExpression("/script-src[^;]*'nonce-[^']+'/", $csp);

        preg_match('/script-src([^;]*)/', $csp, $matches);
        $this->assertStringNotContainsString('unsafe-inline', $matches[1] ?? '');
    }

    public function test_the_csp_nonce_matches_the_nonce_rendered_in_the_login_page(): void
    {
        $response = $this->get('/login');

        $csp = (string) $response->headers->get('Content-Security-Policy');
        preg_match("/script-src[^;]*'nonce-([^']+)'/", $csp, $matches);
        $headerNonce = $matches[1] ?? null;

        $this->assertNotNull($headerNonce);
        $response->assertSee('nonce="'.$headerNonce.'"', false);
    }

    public function test_hsts_is_absent_over_plain_http(): void
    {
        $response = $this->get('/');

        $response->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_hsts_is_present_over_https(): void
    {
        $response = $this->get('https://localhost/', ['HTTPS' => 'on']);

        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
    }
}
