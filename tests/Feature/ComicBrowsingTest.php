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

    public function test_sitemap_and_feed_render(): void
    {
        Comic::factory()->create(['slug' => 'x', 'published_at' => '2026-06-20']);

        $this->get('/sitemap.xml')->assertOk()->assertSee('/comic/x');
        $this->get('/feed')->assertOk()->assertSee('<rss', false);
    }
}
