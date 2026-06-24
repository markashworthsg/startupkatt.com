<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class NewsletterTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('comics.beehiiv.api_key', 'test-key');
        config()->set('comics.beehiiv.publication_id', 'pub_123');
    }

    public function test_valid_email_creates_a_beehiiv_subscription(): void
    {
        Http::fake([
            'api.beehiiv.com/*' => Http::response(['data' => ['status' => 'active']], 201),
        ]);

        $response = $this->postJson(route('newsletter.subscribe'), [
            'email' => 'founder@example.com',
        ]);

        $response->assertOk()->assertJson(['ok' => true]);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/publications/pub_123/subscriptions')
                && $request['email'] === 'founder@example.com'
                && $request->hasHeader('Authorization', 'Bearer test-key');
        });
    }

    public function test_bare_publication_id_is_normalised_with_the_pub_prefix(): void
    {
        config()->set('comics.beehiiv.publication_id', 'abc-123');

        Http::fake([
            'api.beehiiv.com/*' => Http::response(['data' => ['status' => 'active']], 201),
        ]);

        $this->postJson(route('newsletter.subscribe'), ['email' => 'founder@example.com'])
            ->assertOk();

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/publications/pub_abc-123/subscriptions');
        });
    }

    public function test_invalid_email_is_rejected_without_calling_beehiiv(): void
    {
        Http::fake();

        $this->postJson(route('newsletter.subscribe'), ['email' => 'not-an-email'])
            ->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_honeypot_submission_is_rejected(): void
    {
        Http::fake();

        $this->postJson(route('newsletter.subscribe'), [
            'email' => 'bot@example.com',
            'company' => 'Acme Inc',
        ])->assertStatus(422);

        Http::assertNothingSent();
    }

    public function test_beehiiv_failure_returns_a_friendly_error(): void
    {
        Http::fake([
            'api.beehiiv.com/*' => Http::response(['error' => 'nope'], 500),
        ]);

        $this->postJson(route('newsletter.subscribe'), ['email' => 'founder@example.com'])
            ->assertStatus(502)
            ->assertJson(['ok' => false]);
    }

    public function test_unconfigured_beehiiv_returns_service_unavailable(): void
    {
        config()->set('comics.beehiiv.api_key', null);
        Http::fake();

        $this->postJson(route('newsletter.subscribe'), ['email' => 'founder@example.com'])
            ->assertStatus(503);

        Http::assertNothingSent();
    }
}
