<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityHeadersTest extends TestCase
{
    use RefreshDatabase;

    public function test_hsts_and_upgrade_headers_are_sent_over_https(): void
    {
        $response = $this->get('https://localhost/');

        $response->assertOk();
        $response->assertHeader('Strict-Transport-Security', 'max-age=31536000');
        $response->assertHeader('Content-Security-Policy', 'upgrade-insecure-requests');
    }

    public function test_hsts_is_not_sent_over_plain_http(): void
    {
        // Guards local http dev and the browser's localhost cache from being
        // poisoned with HSTS (which would force https on localhost).
        $response = $this->get('http://localhost/');

        $response->assertOk();
        $response->assertHeaderMissing('Strict-Transport-Security');
        $response->assertHeaderMissing('Content-Security-Policy');
    }
}
