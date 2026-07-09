<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_upgrade_insecure_requests_is_sent_over_https(): void
    {
        $response = $this->get('https://localhost/');

        $response->assertOk();
        $response->assertHeader('Content-Security-Policy', 'upgrade-insecure-requests');
    }

    public function test_hsts_is_never_sent(): void
    {
        // HSTS was removed: Singtel intercepts this hostname's TLS, and HSTS
        // turned that into an unbypassable hard block for Singtel visitors.
        $response = $this->get('https://localhost/');

        $response->assertOk();
        $response->assertHeaderMissing('Strict-Transport-Security');
    }

    public function test_no_headers_over_plain_http(): void
    {
        $response = $this->get('http://localhost/');

        $response->assertOk();
        $response->assertHeaderMissing('Content-Security-Policy');
    }
}
