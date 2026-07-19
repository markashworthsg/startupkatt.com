<?php

namespace Tests\Feature;

use App\Models\Comic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ComicBrowsingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-06-21 12:00:00');
    }

    public function test_homepage_shows_the_latest_published_comic(): void
    {
        Comic::factory()->create(['number' => 1, 'slug' => 'one', 'title' => 'One', 'published_at' => '2026-06-19']);
        $latest = Comic::factory()->create(['number' => 2, 'slug' => 'two', 'title' => 'Two', 'published_at' => '2026-06-21']);
        // A future comic that must not appear.
        Comic::factory()->create(['number' => 3, 'slug' => 'three', 'title' => 'Three', 'published_at' => '2026-06-30']);

        $this->get('/')
            ->assertOk()
            ->assertSee('Two')
            ->assertDontSee('Three');
    }

    public function test_a_future_comic_returns_404(): void
    {
        Comic::factory()->create(['slug' => 'future', 'published_at' => '2026-12-25']);

        $this->get('/comic/future')->assertNotFound();
    }

    public function test_a_published_comic_is_viewable_with_json_ld(): void
    {
        Comic::factory()->create(['slug' => 'live', 'title' => 'Live One', 'published_at' => '2026-06-20']);

        $this->get('/comic/live')
            ->assertOk()
            ->assertSee('Live One')
            ->assertSee('ComicStory'); // structured data present
    }

    public function test_prev_and_next_navigation(): void
    {
        $a = Comic::factory()->create(['number' => 1, 'slug' => 'a', 'published_at' => '2026-06-18']);
        $b = Comic::factory()->create(['number' => 2, 'slug' => 'b', 'published_at' => '2026-06-19']);
        $c = Comic::factory()->create(['number' => 3, 'slug' => 'c', 'published_at' => '2026-06-20']);

        $this->assertNull($a->previous());
        $this->assertEquals($b->id, $a->next()->id);
        $this->assertEquals($a->id, $b->previous()->id);
        $this->assertEquals($c->id, $b->next()->id);
        $this->assertNull($c->next());
    }

    public function test_next_never_points_to_an_unpublished_comic(): void
    {
        $today = Comic::factory()->create(['number' => 1, 'slug' => 'today', 'published_at' => '2026-06-21']);
        Comic::factory()->create(['number' => 2, 'slug' => 'tomorrow', 'published_at' => '2026-06-22']);

        $this->assertNull($today->next());
    }

    public function test_random_redirects_to_a_published_comic(): void
    {
        $a = Comic::factory()->create(['slug' => 'a', 'published_at' => '2026-06-18']);
        $b = Comic::factory()->create(['slug' => 'b', 'published_at' => '2026-06-19']);
        // A future strip must never be a random destination.
        Comic::factory()->create(['slug' => 'future', 'published_at' => '2026-12-25']);

        $res = $this->get('/random')->assertRedirect();
        $this->assertContains($res->headers->get('Location'), [$a->url, $b->url]);
        $this->assertStringNotContainsString('/comic/future', (string) $res->headers->get('Location'));
    }

    public function test_random_excludes_the_current_strip(): void
    {
        $a = Comic::factory()->create(['slug' => 'a', 'published_at' => '2026-06-18']);
        $b = Comic::factory()->create(['slug' => 'b', 'published_at' => '2026-06-19']);

        // Excluding $a should always land on $b (the only other published strip).
        $this->get('/random?not='.$a->id)->assertRedirect($b->url);
    }

    public function test_random_with_a_single_comic_still_redirects_to_it(): void
    {
        $only = Comic::factory()->create(['slug' => 'only', 'published_at' => '2026-06-18']);

        // Even when ?not excludes the only strip, fall back rather than 404.
        $this->get('/random?not='.$only->id)->assertRedirect($only->url);
    }

    public function test_sitemap_and_feed_render(): void
    {
        Comic::factory()->create(['slug' => 'x', 'published_at' => '2026-06-20']);

        $this->get('/sitemap.xml')->assertOk()->assertSee('/comic/x');
        $this->get('/feed')->assertOk()->assertSee('<rss', false);
    }

    public function test_feed_does_not_leak_the_transcript_and_spoil_the_joke(): void
    {
        Comic::factory()->create([
            'slug' => 'punchline',
            'published_at' => '2026-06-20',
            'alt_text' => 'A cat at a whiteboard.',
            'caption' => 'Panel 3: the cat says the secret punchline.',
        ]);

        $res = $this->get('/feed')->assertOk();
        // The image + read link ship; the transcript stays sr-only on-site only.
        $res->assertSee('/comic/punchline');
        $res->assertDontSee('the secret punchline');
    }
}
