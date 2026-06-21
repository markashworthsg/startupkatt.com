# Startup Katt 🐱

The home of **Startup Katt** — a daily AI-generated webcomic about a cat trying to build a startup. (His friends call him Startup Cat. Nobody calls him Startup Katt.)

Laravel 12 app with a one-strip-at-a-time reader, a per-comic SEO/AEO-optimized URL for every strip, an automatic **folder-drop** publishing pipeline, RSS feed, and XML sitemap. Built to keep extending with Windsurf / Devin / Claude.

---

## Quick start

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate

# 3. Database (SQLite by default — zero setup)
touch database/database.sqlite      # already present in this repo, but just in case
php artisan migrate

# 4. Make uploaded art web-accessible
php artisan storage:link

# 5. Build assets + run
npm run dev        # in one terminal (Vite)
php artisan serve  # in another → http://127.0.0.1:8000
```

## Publishing comics — the folder-drop workflow

1. Drop image files (`.png`, `.jpg`, `.jpeg`, `.gif`, `.webp`) into:
   ```
   storage/app/comics/incoming/
   ```
2. Run the importer:
   ```bash
   php artisan comics:import          # add --dry-run to preview
   ```

What happens:

- New files are de-duplicated by content hash (re-running is safe).
- New files are ordered by **file modification time, earliest first**.
- Each one is assigned the **next empty future release date**, one comic per day.
  - The earliest file takes the earliest open slot. With `COMICS_FIRST_SLOT=today`, the most recent batch's first strip goes live **today**; the next goes out tomorrow, and so on.
  - If you've fallen behind (the latest scheduled date is in the past), it catches up to today rather than backfilling gaps.
- Art is copied to `storage/app/public/comics/{year}/{month}/` and a `Comic` row is created with a slug, sequential number, dimensions, and placeholder alt text.

> **Tip:** after import, edit each comic's `title`, `alt_text`, `caption`, and `description` for best SEO/AEO and accessibility. Placeholder values are generated automatically so nothing is ever blank.

### Automating it

A scheduler entry already runs the import daily (`routes/console.php`). Add one cron line on your server:

```cron
* * * * * cd /path-to/startupkatt.com && php artisan schedule:run >> /dev/null 2>&1
```

## Routes

| URL | What |
| --- | --- |
| `/` | Latest published comic (the reader) |
| `/comic/{slug}` | A single comic — its own SEO page |
| `/archive` | Grid of every published comic |
| `/feed` | RSS 2.0 feed (also drives beehiiv RSS-to-email) |
| `/sitemap.xml` | XML sitemap of all published comics |
| `/robots.txt` | Allows search + AI/answer engines |

Future-dated comics return **404** until their release date, so nothing leaks early.

## SEO / AEO

Every comic page ships per-strip `<title>` + meta description, canonical URL, Open Graph + Twitter card tags, and `ComicStory` JSON-LD structured data. The sitemap and RSS feed update automatically. `robots.txt` explicitly welcomes Googlebot plus GPTBot, ClaudeBot, PerplexityBot, etc. so answer engines can cite the strips.

Add a default social image at `public/og-default.png` (1200×630) for pages without a comic image.

## beehiiv (optional newsletter layer)

This site is built to run standalone, but it has drop-in hooks for a beehiiv newsletter. Set `BEEHIIV_EMBED_URL` in `.env` to show the signup form. See **CLAUDE.md → beehiiv integration** for the full hybrid setup (RSS-to-email needs zero code).

## Testing

```bash
php artisan test
```

Covers the forward-fill scheduling logic, de-duplication, catch-up behaviour, 404-ing future comics, prev/next navigation, and the feed/sitemap.

## Stack

Laravel 12 · PHP 8.2+ · Blade · Tailwind CSS v4 (Vite) · SQLite (swap to MySQL via `.env`).
