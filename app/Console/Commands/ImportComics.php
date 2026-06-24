<?php

namespace App\Console\Commands;

use App\Models\Comic;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImportComics extends Command
{
    protected $signature = 'comics:import
                            {--dry-run : Show what would happen without writing anything}';

    protected $description = 'Scan the incoming folder and schedule new comics onto the next empty future dates';

    public function handle(): int
    {
        $incoming = config('comics.incoming_path');
        $dryRun = (bool) $this->option('dry-run');

        if (! File::isDirectory($incoming)) {
            $this->error("Incoming folder does not exist: {$incoming}");

            return self::FAILURE;
        }

        // 1. Collect candidate image files.
        $extensions = collect(config('comics.extensions'))
            ->map(fn ($e) => strtolower($e))
            ->all();

        $files = collect(File::files($incoming))
            ->filter(fn ($f) => in_array(strtolower($f->getExtension()), $extensions, true));

        if ($files->isEmpty()) {
            $this->info('No image files found in the incoming folder. Nothing to do.');

            return self::SUCCESS;
        }

        // 2. Hash each file and drop anything we've already imported.
        $existingHashes = Comic::pluck('file_hash')->flip();

        $candidates = $files
            ->map(fn ($f) => [
                'file'  => $f,
                'hash'  => hash_file('sha256', $f->getRealPath()),
                'mtime' => $f->getMTime(),
            ])
            ->reject(fn ($c) => $existingHashes->has($c['hash']))
            // De-dupe within this same batch (two identical files dropped at once).
            ->unique('hash')
            // 3. Order by file modification time, EARLIEST first, the earliest
            //    file takes the earliest open release slot.
            ->sortBy('mtime')
            ->values();

        if ($candidates->isEmpty()) {
            $this->info('All files in the incoming folder have already been imported.');

            return self::SUCCESS;
        }

        // 4. Work out the first open release date and walk forward one day each.
        $slot = $this->nextSlot();
        $number = (int) (Comic::max('number') ?? 0);

        $this->line("Importing {$candidates->count()} new comic(s). First release slot: {$slot->toDateString()}");

        foreach ($candidates as $c) {
            $number++;
            $result = $this->importOne($c['file'], $c['hash'], $c['mtime'], $slot, $number, $dryRun);
            $this->line(sprintf(
                '  %s  #%-4d  %s  → %s',
                $dryRun ? '[dry]' : '  ✓  ',
                $number,
                str_pad(Str::limit($result['slug'], 28), 28),
                $slot->toDateString()
            ));
            $slot = $slot->copy()->addDay();
        }

        $this->newLine();
        $this->info($dryRun
            ? 'Dry run complete. No changes written.'
            : "Done. {$candidates->count()} comic(s) scheduled.");

        return self::SUCCESS;
    }

    /**
     * The earliest empty future release date.
     *
     * Forward-fill rule: continue the day after the latest scheduled comic.
     * If we've fallen behind (latest scheduled date is in the past) or there
     * are no comics yet, start from the configured first slot (today/tomorrow).
     */
    protected function nextSlot(): Carbon
    {
        $base = config('comics.first_slot') === 'tomorrow'
            ? Carbon::tomorrow()
            : Carbon::today();

        $last = Comic::max('published_at');

        if (! $last) {
            return $base;
        }

        $afterLast = Carbon::parse($last)->addDay()->startOfDay();

        // Never backfill into the past; never start earlier than the base slot.
        return $afterLast->lt($base) ? $base : $afterLast;
    }

    /**
     * @param  \Symfony\Component\Finder\SplFileInfo  $file
     * @return array{slug: string}
     */
    protected function importOne($file, string $hash, int $mtime, Carbon $slot, int $number, bool $dryRun): array
    {
        $base = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        $ext = strtolower($file->getExtension());

        $slug = $this->uniqueSlug(Str::slug($base) ?: 'comic-'.$number);
        $title = Str::headline($base);

        if ($dryRun) {
            return ['slug' => $slug];
        }

        // Copy art onto the public disk: comics/{Y}/{m}/{slug}.{ext}
        $dir = trim(config('comics.public_dir'), '/')
            .'/'.$slot->format('Y').'/'.$slot->format('m');
        $destination = "{$dir}/{$slug}.{$ext}";

        Storage::disk('public')->put(
            $destination,
            File::get($file->getRealPath())
        );

        [$width, $height] = $this->imageDimensions($file->getRealPath());

        Comic::create([
            'number'            => $number,
            'slug'              => $slug,
            'title'             => $title,
            // Sensible placeholders, edit later for best SEO/AEO + accessibility.
            'alt_text'          => $title.': Startup Katt comic',
            'caption'           => null,
            'description'       => null,
            'image_path'        => $destination,
            'og_image_path'     => null,
            'width'             => $width,
            'height'            => $height,
            'file_hash'         => $hash,
            'original_filename' => $file->getFilename(),
            'file_created_at'   => Carbon::createFromTimestamp($mtime),
            'published_at'      => $slot->toDateString(),
        ]);

        if (config('comics.move_after_import')) {
            File::delete($file->getRealPath());
        }

        return ['slug' => $slug];
    }

    protected function uniqueSlug(string $slug): string
    {
        $original = $slug;
        $i = 2;

        while (Comic::where('slug', $slug)->exists()) {
            $slug = "{$original}-{$i}";
            $i++;
        }

        return $slug;
    }

    /** @return array{0: ?int, 1: ?int} */
    protected function imageDimensions(string $path): array
    {
        $size = @getimagesize($path);

        return $size ? [$size[0], $size[1]] : [null, null];
    }
}
