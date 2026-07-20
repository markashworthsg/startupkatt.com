<?php

namespace Tests\Feature;

use App\Models\Comic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class TelegramPostTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'comics.telegram.bot_token'     => 'test-token',
            'comics.telegram.channel'       => '@startupkatt',
            'comics.telegram.catch_up_days' => 3,
            'comics.telegram.max_per_run'   => 5,
        ]);
    }

    /** Telegram's success envelope. */
    protected function fakeOk(int $messageId = 4242): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok'     => true,
                'result' => ['message_id' => $messageId],
            ]),
        ]);
    }

    public function test_it_posts_a_newly_published_strip_and_stamps_it(): void
    {
        $this->fakeOk();
        $comic = Comic::factory()->published()->create();

        $this->artisan('comics:post-telegram')->assertSuccessful();

        $comic->refresh();
        $this->assertNotNull($comic->telegram_posted_at);
        $this->assertSame(4242, (int) $comic->telegram_message_id);

        Http::assertSent(function ($request) use ($comic) {
            return str_contains($request->url(), '/bottest-token/sendPhoto')
                && $request['chat_id'] === '@startupkatt'
                && $request['photo'] === $comic->image_url
                && str_contains($request['caption'], $comic->title)
                && str_contains($request['caption'], $comic->url);
        });
    }

    public function test_it_never_posts_the_same_strip_twice(): void
    {
        $this->fakeOk();
        Comic::factory()->published()->create();

        $this->artisan('comics:post-telegram')->assertSuccessful();
        $this->artisan('comics:post-telegram')->assertSuccessful();

        Http::assertSentCount(1);
    }

    public function test_it_does_not_post_scheduled_strips(): void
    {
        $this->fakeOk();
        $comic = Comic::factory()->scheduled()->create();

        $this->artisan('comics:post-telegram')->assertSuccessful();

        Http::assertNothingSent();
        $this->assertNull($comic->refresh()->telegram_posted_at);
    }

    public function test_it_ignores_the_archive_unless_backfilling(): void
    {
        $this->fakeOk();
        $old = Comic::factory()->create(['published_at' => now()->subMonths(2)->toDateString()]);

        $this->artisan('comics:post-telegram')->assertSuccessful();
        Http::assertNothingSent();

        $this->artisan('comics:post-telegram', ['--all' => true])->assertSuccessful();
        Http::assertSentCount(1);
        $this->assertNotNull($old->refresh()->telegram_posted_at);
    }

    public function test_it_catches_up_on_a_missed_run(): void
    {
        $this->fakeOk();
        $missed = Comic::factory()->create(['published_at' => now()->subDay()->toDateString()]);

        $this->artisan('comics:post-telegram')->assertSuccessful();

        $this->assertNotNull($missed->refresh()->telegram_posted_at);
    }

    public function test_it_respects_the_per_run_cap(): void
    {
        $this->fakeOk();
        config(['comics.telegram.max_per_run' => 2]);

        foreach (range(0, 2) as $i) {
            Comic::factory()->create(['published_at' => now()->subDays($i)->toDateString()]);
        }

        $this->artisan('comics:post-telegram')->assertSuccessful();

        Http::assertSentCount(2);
    }

    public function test_it_is_a_no_op_when_unconfigured(): void
    {
        Http::fake();
        config(['comics.telegram.bot_token' => null]);
        $comic = Comic::factory()->published()->create();

        $this->artisan('comics:post-telegram')->assertSuccessful();

        Http::assertNothingSent();
        $this->assertNull($comic->refresh()->telegram_posted_at);
    }

    public function test_a_rejected_post_is_left_unstamped_for_retry(): void
    {
        Http::fake([
            'api.telegram.org/*' => Http::response([
                'ok'          => false,
                'description' => 'Bad Request: chat not found',
            ], 400),
        ]);

        $comic = Comic::factory()->published()->create();

        $this->artisan('comics:post-telegram')->assertFailed();

        $this->assertNull($comic->refresh()->telegram_posted_at);
    }

    public function test_dry_run_writes_nothing(): void
    {
        Http::fake();
        $comic = Comic::factory()->published()->create();

        $this->artisan('comics:post-telegram', ['--dry-run' => true])->assertSuccessful();

        Http::assertNothingSent();
        $this->assertNull($comic->refresh()->telegram_posted_at);
    }
}
