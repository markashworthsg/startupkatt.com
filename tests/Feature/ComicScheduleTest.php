<?php

namespace Tests\Feature;

use App\Models\Comic;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ComicScheduleTest extends TestCase
{
    use RefreshDatabase;

    protected string $incoming;

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('public');

        $this->incoming = storage_path('framework/testing/incoming');
        File::ensureDirectoryExists($this->incoming);
        File::cleanDirectory($this->incoming);

        config()->set('comics.incoming_path', $this->incoming);
        config()->set('comics.first_slot', 'today');
        config()->set('comics.move_after_import', false);
    }

    /** A 1x1 transparent PNG written to the incoming folder with a set mtime. */
    protected function dropFile(string $name, string $mtime): void
    {
        $png = base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg=='
        );
        $path = $this->incoming.'/'.$name;
        // Append the filename so distinct files get distinct content (and thus
        // distinct sha256 hashes). Identical filenames stay byte-identical, so
        // the importer's de-dupe-by-hash behaviour is still exercised.
        File::put($path, $png.$name);
        touch($path, Carbon::parse($mtime)->getTimestamp());
    }

    public function test_earliest_file_takes_the_earliest_open_slot(): void
    {
        Carbon::setTestNow('2026-06-21 09:00:00');

        // Drop two on the same day; the earlier mtime should release first.
        $this->dropFile('second.png', '2026-06-21 11:00:00');
        $this->dropFile('first.png', '2026-06-21 08:00:00');

        $this->artisan('comics:import')->assertSuccessful();

        $this->assertSame(2, Comic::count());

        $first = Comic::where('original_filename', 'first.png')->first();
        $second = Comic::where('original_filename', 'second.png')->first();

        // first_slot = today, so the earliest file is today, the next is tomorrow.
        $this->assertSame('2026-06-21', $first->published_at->toDateString());
        $this->assertSame('2026-06-22', $second->published_at->toDateString());

        // Sequential numbering follows release order.
        $this->assertSame(1, $first->number);
        $this->assertSame(2, $second->number);
    }

    public function test_new_files_forward_fill_after_the_latest_scheduled_date(): void
    {
        Carbon::setTestNow('2026-06-21 09:00:00');

        // Pretend we're already scheduled out to the 25th.
        Comic::factory()->create(['published_at' => '2026-06-25', 'number' => 1]);

        $this->dropFile('new.png', '2026-06-21 10:00:00');
        $this->artisan('comics:import')->assertSuccessful();

        $new = Comic::where('original_filename', 'new.png')->first();
        $this->assertSame('2026-06-26', $new->published_at->toDateString());
    }

    public function test_it_catches_up_to_today_when_behind(): void
    {
        Carbon::setTestNow('2026-06-21 09:00:00');

        // Latest scheduled date is in the past — don't backfill, start today.
        Comic::factory()->create(['published_at' => '2026-06-10', 'number' => 1]);

        $this->dropFile('catchup.png', '2026-06-21 10:00:00');
        $this->artisan('comics:import')->assertSuccessful();

        $c = Comic::where('original_filename', 'catchup.png')->first();
        $this->assertSame('2026-06-21', $c->published_at->toDateString());
    }

    public function test_duplicate_files_are_not_imported_twice(): void
    {
        Carbon::setTestNow('2026-06-21 09:00:00');

        $this->dropFile('dupe.png', '2026-06-21 10:00:00');
        $this->artisan('comics:import')->assertSuccessful();
        $this->artisan('comics:import')->assertSuccessful();

        $this->assertSame(1, Comic::count());
    }

    public function test_first_slot_tomorrow_skips_today(): void
    {
        Carbon::setTestNow('2026-06-21 09:00:00');
        config()->set('comics.first_slot', 'tomorrow');

        $this->dropFile('a.png', '2026-06-21 10:00:00');
        $this->artisan('comics:import')->assertSuccessful();

        $c = Comic::first();
        $this->assertSame('2026-06-22', $c->published_at->toDateString());
    }
}
