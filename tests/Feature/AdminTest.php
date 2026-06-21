<?php

namespace Tests\Feature;

use App\Models\Comic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Unlock the single HTTP Basic gate for these tests.
        config()->set('comics.admin.username', 'admin');
        config()->set('comics.admin.password', 'secret');
    }

    /** HTTP Basic header for the configured admin credentials. */
    protected function asAdmin(): array
    {
        return ['Authorization' => 'Basic '.base64_encode('admin:secret')];
    }

    public function test_admin_is_locked_when_no_password_is_configured(): void
    {
        config()->set('comics.admin.password', null);

        $this->get('/admin', $this->asAdmin())->assertForbidden();
    }

    public function test_admin_requires_authentication(): void
    {
        $this->get('/admin')->assertStatus(401);
    }

    public function test_admin_rejects_wrong_credentials(): void
    {
        $this->get('/admin', ['Authorization' => 'Basic '.base64_encode('admin:nope')])
            ->assertStatus(401);
    }

    public function test_admin_index_lists_comics_with_valid_credentials(): void
    {
        Comic::factory()->create(['title' => 'The Pivot', 'published_at' => '2026-06-20']);

        $this->get('/admin', $this->asAdmin())
            ->assertOk()
            ->assertSee('The Pivot');
    }

    public function test_admin_index_includes_scheduled_comics(): void
    {
        Comic::factory()->create(['title' => 'Future Strip', 'published_at' => '2099-01-01']);

        $this->get('/admin', $this->asAdmin())
            ->assertOk()
            ->assertSee('Future Strip')
            ->assertSee('Scheduled');
    }

    public function test_edit_page_loads(): void
    {
        $comic = Comic::factory()->create(['slug' => 'the-pivot', 'published_at' => '2026-06-20']);

        $this->get("/admin/comics/{$comic->slug}/edit", $this->asAdmin())
            ->assertOk()
            ->assertSee('Release date');
    }

    public function test_update_persists_edits(): void
    {
        $comic = Comic::factory()->create([
            'slug'         => 'the-pivot',
            'title'        => 'Old Title',
            'published_at' => '2026-06-20',
        ]);

        $response = $this->put("/admin/comics/{$comic->slug}", [
            'title'        => 'Disrupting Naps',
            'alt_text'     => 'Startup Cat naps through a pitch meeting',
            'caption'      => 'Panel 1: zzz.',
            'description'  => 'A new SEO description.',
            'published_at' => '2026-06-20',
        ], $this->asAdmin());

        $response->assertRedirect(route('admin.index'));

        $comic->refresh();
        $this->assertSame('Disrupting Naps', $comic->title);
        $this->assertSame('A new SEO description.', $comic->description);

        // Reflected on the public comic page.
        $this->get('/comic/the-pivot')
            ->assertOk()
            ->assertSee('Disrupting Naps');
    }

    public function test_update_can_change_the_release_date(): void
    {
        $comic = Comic::factory()->create([
            'slug'         => 'the-pivot',
            'published_at' => '2026-06-20',
        ]);

        $this->put("/admin/comics/{$comic->slug}", [
            'title'        => $comic->title,
            'alt_text'     => $comic->alt_text,
            'published_at' => '2026-06-18',
        ], $this->asAdmin())->assertRedirect(route('admin.index'));

        $this->assertSame('2026-06-18', $comic->refresh()->published_at->toDateString());
    }

    public function test_update_rejects_a_date_already_used_by_another_comic(): void
    {
        $taken = Comic::factory()->create(['published_at' => '2026-06-21']);
        $comic = Comic::factory()->create(['slug' => 'the-pivot', 'published_at' => '2026-06-20']);

        $this->put("/admin/comics/{$comic->slug}", [
            'title'        => $comic->title,
            'alt_text'     => $comic->alt_text,
            'published_at' => '2026-06-21', // collides with $taken
        ], $this->asAdmin())->assertSessionHasErrors('published_at');

        // Unchanged.
        $this->assertSame('2026-06-20', $comic->refresh()->published_at->toDateString());
    }

    public function test_update_allows_keeping_the_same_date(): void
    {
        $comic = Comic::factory()->create([
            'slug'         => 'the-pivot',
            'published_at' => '2026-06-20',
        ]);

        $this->put("/admin/comics/{$comic->slug}", [
            'title'        => 'Still Same Day',
            'alt_text'     => $comic->alt_text,
            'published_at' => '2026-06-20', // its own date — must not trip the unique rule
        ], $this->asAdmin())->assertSessionHasNoErrors();

        $this->assertSame('Still Same Day', $comic->refresh()->title);
    }

    public function test_update_requires_title_and_alt_text(): void
    {
        $comic = Comic::factory()->create(['slug' => 'the-pivot', 'published_at' => '2026-06-20']);

        $this->put("/admin/comics/{$comic->slug}", [
            'title'        => '',
            'alt_text'     => '',
            'published_at' => '2026-06-20',
        ], $this->asAdmin())->assertSessionHasErrors(['title', 'alt_text']);
    }
}
