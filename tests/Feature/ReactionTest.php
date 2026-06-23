<?php

namespace Tests\Feature;

use App\Models\Comic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ReactionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Carbon::setTestNow('2026-06-21 12:00:00');
    }

    /** React from a specific source IP (the anti-spam dedup key). */
    protected function reactFrom(string $ip, Comic $comic, string $reaction)
    {
        return $this
            ->withServerVariables(['REMOTE_ADDR' => $ip])
            ->postJson("/comic/{$comic->slug}/react", ['reaction' => $reaction]);
    }

    public function test_voting_increments_the_count_and_returns_json(): void
    {
        $comic = Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        $this->reactFrom('10.0.0.1', $comic, 'funny')
            ->assertOk()
            ->assertJsonPath('counts.funny', 1)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('userReaction', 'funny');

        $this->assertDatabaseHas('reactions', [
            'comic_id' => $comic->id,
            'reaction' => 'funny',
            'count'    => 1,
        ]);
    }

    public function test_changing_your_vote_moves_the_count(): void
    {
        $comic = Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        $this->reactFrom('10.0.0.1', $comic, 'funny');

        $this->reactFrom('10.0.0.1', $comic, 'gross')
            ->assertOk()
            ->assertJsonPath('counts.funny', 0)
            ->assertJsonPath('counts.gross', 1)
            ->assertJsonPath('total', 1)
            ->assertJsonPath('userReaction', 'gross');
    }

    public function test_tapping_your_current_reaction_toggles_it_off(): void
    {
        $comic = Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        $this->reactFrom('10.0.0.1', $comic, 'love');

        $this->reactFrom('10.0.0.1', $comic, 'love')
            ->assertOk()
            ->assertJsonPath('counts.love', 0)
            ->assertJsonPath('total', 0)
            ->assertJsonPath('userReaction', null);
    }

    public function test_one_ip_cannot_inflate_a_strip_no_matter_how_it_votes(): void
    {
        $comic = Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        // Same IP fires a flurry of reactions — counts only ever reflect one vote.
        $this->reactFrom('10.0.0.1', $comic, 'funny');
        $this->reactFrom('10.0.0.1', $comic, 'gross');
        $this->reactFrom('10.0.0.1', $comic, 'love');
        $this->reactFrom('10.0.0.1', $comic, 'unhinged')
            ->assertJsonPath('total', 1)
            ->assertJsonPath('counts.unhinged', 1);

        $this->assertDatabaseCount('reaction_votes', 1);
    }

    public function test_clearing_the_cookie_does_not_let_one_ip_revote(): void
    {
        $comic = Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        // First vote from this IP.
        $this->reactFrom('10.0.0.1', $comic, 'funny')->assertJsonPath('counts.funny', 1);

        // Same IP, brand-new client with no cookie, votes a different reaction:
        // it moves the existing vote rather than adding a second one.
        $this->flushSession();
        $this->reactFrom('10.0.0.1', $comic, 'love')
            ->assertJsonPath('counts.funny', 0)
            ->assertJsonPath('counts.love', 1)
            ->assertJsonPath('total', 1);
    }

    public function test_different_ips_each_get_a_vote(): void
    {
        $comic = Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        $this->reactFrom('10.0.0.1', $comic, 'funny');
        $this->reactFrom('10.0.0.2', $comic, 'funny');
        $this->reactFrom('10.0.0.3', $comic, 'funny')
            ->assertJsonPath('counts.funny', 3)
            ->assertJsonPath('total', 3);

        $this->assertDatabaseCount('reaction_votes', 3);
    }

    public function test_the_endpoint_is_rate_limited(): void
    {
        $comic = Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        // The 20/min budget is exhausted by hammering, then further votes 429.
        for ($i = 0; $i < 20; $i++) {
            $this->reactFrom('10.0.0.9', $comic, 'funny')->assertOk();
        }

        $this->reactFrom('10.0.0.9', $comic, 'funny')->assertStatus(429);
    }

    public function test_an_unknown_reaction_is_rejected(): void
    {
        $comic = Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        $this->reactFrom('10.0.0.1', $comic, 'sponsored')
            ->assertStatus(422);

        $this->assertDatabaseCount('reactions', 0);
        $this->assertDatabaseCount('reaction_votes', 0);
    }

    public function test_you_cannot_react_to_a_future_strip(): void
    {
        $comic = Comic::factory()->create(['slug' => 'future', 'published_at' => '2026-12-25']);

        $this->reactFrom('10.0.0.1', $comic, 'funny')
            ->assertNotFound();

        $this->assertDatabaseCount('reactions', 0);
        $this->assertDatabaseCount('reaction_votes', 0);
    }

    public function test_the_unhinged_reaction_is_offered_on_the_page(): void
    {
        Comic::factory()->create(['slug' => 'live', 'published_at' => '2026-06-20']);

        $this->get('/comic/live')
            ->assertOk()
            ->assertSee('Disturbingly relatable');
    }
}
