<?php

namespace Tests\Feature;

use App\Models\Comic;
use App\Models\Reaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TopStripsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-06-21 12:00:00');
    }

    /** Make a published strip carrying the given per-reaction tallies. */
    protected function strip(string $slug, string $date, array $tallies): Comic
    {
        $comic = Comic::factory()->create([
            'slug'         => $slug,
            'title'        => ucfirst($slug),
            'published_at' => $date,
        ]);

        foreach ($tallies as $reaction => $count) {
            Reaction::create(['comic_id' => $comic->id, 'reaction' => $reaction, 'count' => $count]);
        }

        return $comic;
    }

    public function test_overall_leaderboard_ranks_by_total_reactions(): void
    {
        $this->strip('quiet', '2026-06-10', ['funny' => 1]);
        $this->strip('loud', '2026-06-11', ['funny' => 5, 'iconic' => 5]); // total 10
        $this->strip('middle', '2026-06-12', ['facts' => 4]);               // total 4

        $top = Comic::topOverall();

        $this->assertSame(['loud', 'middle', 'quiet'], $top->pluck('slug')->all());
        $this->assertSame(10, (int) $top->first()->reactions_total);
    }

    public function test_strips_with_no_reactions_are_excluded(): void
    {
        $this->strip('voted', '2026-06-10', ['funny' => 2]);
        Comic::factory()->create(['slug' => 'silent', 'published_at' => '2026-06-11']);

        $this->assertSame(['voted'], Comic::topOverall()->pluck('slug')->all());
    }

    public function test_per_reaction_leaderboard_ranks_by_that_reaction(): void
    {
        $this->strip('a', '2026-06-10', ['funny' => 2, 'real' => 9]);
        $this->strip('b', '2026-06-11', ['funny' => 8, 'real' => 1]);

        $this->assertSame(['b', 'a'], Comic::topByReaction('funny')->pluck('slug')->all());
        $this->assertSame(['a', 'b'], Comic::topByReaction('real')->pluck('slug')->all());
    }

    public function test_the_top_page_renders_with_a_reaction_filter(): void
    {
        $this->strip('a', '2026-06-10', ['unhinged' => 3]);
        $this->strip('b', '2026-06-11', ['unhinged' => 7]);
        $this->strip('c', '2026-06-12', ['unhinged' => 5]);

        $this->get('/top/unhinged')
            ->assertOk()
            ->assertSee('most unhinged')
            ->assertSee('ItemList')         // structured data present
            ->assertSee('FAQPage')          // FAQ schema present
            ->assertDontSee('noindex');     // 3 ranked strips => indexable
    }

    public function test_an_unknown_reaction_leaderboard_404s(): void
    {
        $this->get('/top/sponsored')->assertNotFound();
    }

    public function test_a_thin_leaderboard_is_noindexed(): void
    {
        $this->strip('a', '2026-06-10', ['funny' => 1]);

        $this->get('/top')
            ->assertOk()
            ->assertSee('noindex'); // fewer than 3 ranked strips
    }

    public function test_the_toast_teases_the_top_strip_and_links_to_the_leaderboard(): void
    {
        $this->strip('runner-up', '2026-06-10', ['funny' => 2]);
        $this->strip('chart-topper', '2026-06-11', ['funny' => 50]);
        $viewing = $this->strip('today', '2026-06-20', ['iconic' => 1]);

        $this->get($viewing->url)
            ->assertOk()
            ->assertSee('data-top-toast', false)
            ->assertSee('Reader favourite')
            ->assertSee('Chart-topper')        // the most-reacted strip
            ->assertSee(route('top'));
    }

    public function test_the_toast_does_not_tease_the_strip_you_are_reading(): void
    {
        $top = $this->strip('chart-topper', '2026-06-11', ['funny' => 50]);

        $this->get($top->url)
            ->assertOk()
            ->assertDontSee('data-top-toast', false);
    }

    public function test_the_sitemap_lists_the_leaderboard_when_it_has_enough_data(): void
    {
        $this->strip('a', '2026-06-10', ['funny' => 3]);
        $this->strip('b', '2026-06-11', ['funny' => 2]);
        $this->strip('c', '2026-06-12', ['funny' => 1]);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('top'), false)
            ->assertSee(route('top', 'funny'), false);
    }
}
