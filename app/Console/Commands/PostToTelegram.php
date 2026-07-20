<?php

namespace App\Console\Commands;

use App\Models\Comic;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PostToTelegram extends Command
{
    protected $signature = 'comics:post-telegram
                            {--dry-run : Show what would be posted without calling Telegram}
                            {--all : Consider every published strip, not just recent ones (backfill)}
                            {--limit= : Override the per-run cap}';

    protected $description = 'Push newly published strips to the Telegram channel';

    public function handle(): int
    {
        $token = config('comics.telegram.bot_token');
        $channel = config('comics.telegram.channel');

        // Env-gated, same as beehiiv/Plausible: unconfigured is a clean no-op,
        // not an error, so the daily scheduler stays quiet on a fresh install.
        if (! $token || ! $channel) {
            $this->info('Telegram is not configured (TELEGRAM_BOT_TOKEN / TELEGRAM_CHANNEL). Nothing to do.');

            return self::SUCCESS;
        }

        $comics = $this->pending();

        if ($comics->isEmpty()) {
            $this->info('No unposted strips. Telegram is up to date.');

            return self::SUCCESS;
        }

        $dryRun = (bool) $this->option('dry-run');
        $posted = 0;

        foreach ($comics as $comic) {
            if ($dryRun) {
                $this->line("  [dry]  #{$comic->number}  {$comic->slug}  → {$channel}");
                $posted++;

                continue;
            }

            if ($this->post($comic, $token, $channel)) {
                $this->line("    ✓    #{$comic->number}  {$comic->slug}  → {$channel}");
                $posted++;

                continue;
            }

            // Leave telegram_posted_at null so the next run retries this strip,
            // but stop here: a failure is usually the token, the bot's admin
            // rights, or a rate limit, and none of those improve by hammering.
            $this->error("    ✗    #{$comic->number}  {$comic->slug}  failed, will retry next run.");

            return self::FAILURE;
        }

        $this->newLine();
        $this->info($dryRun
            ? "Dry run complete. {$posted} strip(s) would post. No changes written."
            : "Done. {$posted} strip(s) posted to {$channel}.");

        return self::SUCCESS;
    }

    /**
     * Published strips we have not posted yet, oldest first so the channel
     * reads in chronological order when catching up.
     *
     * @return Collection<int, Comic>
     */
    protected function pending(): Collection
    {
        $limit = (int) ($this->option('limit') ?: config('comics.telegram.max_per_run'));

        return Comic::query()
            ->published()
            ->whereNull('telegram_posted_at')
            // Without --all, only look back a few days. This is what stops a
            // first run (or a re-enable) from dumping the entire archive into
            // the channel: old strips stay unposted unless asked for.
            ->unless($this->option('all'), fn ($q) => $q->whereDate(
                'published_at',
                '>=',
                Carbon::today()->subDays(max(0, (int) config('comics.telegram.catch_up_days')))
            ))
            ->orderBy('published_at')
            ->limit(max(1, $limit))
            ->get();
    }

    /** Send one strip as a channel photo. Returns false if Telegram refused. */
    protected function post(Comic $comic, string $token, string $channel): bool
    {
        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(15)
                ->post("https://api.telegram.org/bot{$token}/sendPhoto", [
                    'chat_id'    => $channel,
                    // Telegram fetches this itself, so it must be publicly
                    // reachable. APP_URL has to be the real domain in prod.
                    'photo'      => $comic->image_url,
                    'caption'    => $this->caption($comic),
                    'parse_mode' => 'HTML',
                ]);
        } catch (\Throwable $e) {
            Log::warning('Telegram post failed', ['comic' => $comic->slug, 'error' => $e->getMessage()]);

            return false;
        }

        if (! $response->successful() || ! ($response->json('ok') ?? false)) {
            Log::warning('Telegram post rejected', [
                'comic'  => $comic->slug,
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);

            return false;
        }

        $comic->forceFill([
            'telegram_posted_at'  => Carbon::now(),
            'telegram_message_id' => $response->json('result.message_id'),
        ])->save();

        return true;
    }

    /**
     * Caption for the channel post. Telegram caps captions at 1024 characters,
     * so the transcript is deliberately left out: the strip is the punchline,
     * and the link earns the click back to the site.
     */
    protected function caption(Comic $comic): string
    {
        $title = e($comic->title);
        $url = e($comic->url);

        return "<b>{$title}</b>\n\nStartup Katt, strip #{$comic->number}.\n{$url}";
    }
}
