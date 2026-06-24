# CLAUDE.md: Startup Katt

Context for AI coding agents (Claude, Devin, Windsurf) working in this repo. Read this before making changes.

## What this is

A Laravel 12 site that hosts the **Startup Katt** webcomic. Core ideas:

- One strip shown at a time, browsable forward/back, first/prev/next/latest.
- Every comic has its own SEO/AEO-optimized URL: `/comic/{slug}`.
- Publishing is **folder-drop**: art goes into `storage/app/comics/incoming/`, and `php artisan comics:import` schedules it onto the next empty future dates (one comic per calendar day).

Running gag: the cat is "Startup Katt" but everyone calls him "Startup Cat". Keep that voice in copy.

## Architecture map

| Concern | File |
| --- | --- |
| Routes | `routes/web.php`, `routes/console.php` (scheduler) |
| Model | `app/Models/Comic.php` |
| Migration | `database/migrations/2026_01_01_000000_create_comics_table.php` |
| Import / scheduling | `app/Console/Commands/ImportComics.php` |
| Pages | `app/Http/Controllers/ComicController.php` |
| Admin (metadata editor) | `app/Http/Controllers/Admin/ComicAdminController.php`, `app/Http/Middleware/AdminAuth.php`, `resources/views/admin/*` |
| Reactions (login-free voting) | `app/Http/Controllers/ReactionController.php`, `app/Models/{Reaction,ReactionVote}.php`, `resources/views/components/comic-reactions.blade.php` |
| Top-strips leaderboard + toast | `app/Http/Controllers/TopController.php`, `resources/views/comics/top.blade.php`, `resources/views/components/top-strip-toast.blade.php` |
| Feed / sitemap | `app/Http/Controllers/{FeedController,SitemapController}.php` |
| Config (all knobs) | `config/comics.php` |
| Reader view | `resources/views/comics/show.blade.php` |
| Nav component | `resources/views/components/comic-nav.blade.php` |
| Newsletter hook | `resources/views/components/newsletter-signup.blade.php` |
| Layout + meta | `resources/views/layouts/app.blade.php` |
| Tests | `tests/Feature/*` |

## The scheduling rule (don't break this)

In `ImportComics::nextSlot()` + the import loop:

1. Candidate files = images in the incoming folder whose sha256 isn't already in the DB.
2. Sort candidates by **file mtime ascending** (earliest file → earliest slot).
3. First slot = day after the latest scheduled `published_at`; if that's in the past, or there are no comics, use the configured base (`today` or `tomorrow`).
4. Walk forward one calendar day per comic. `published_at` is **unique**: exactly one comic per day.

The logic is covered by `tests/Feature/ComicScheduleTest.php`. If you change scheduling, update those tests.

## Conventions

- **Published vs scheduled:** a comic is live only when `published_at <= today`. Always read comics through the `published()` scope on public-facing pages. Future comics must 404 (see `ComicController::show`).
- **Navigation** is by `published_at` order, restricted to the published set (`Comic::previous()/next()`).
- **Images** live on the `public` disk under `comics/{Y}/{m}/`. Run `php artisan storage:link` once. Use `$comic->image_url` / `$comic->og_image_url`, never hardcode paths.
- **SEO:** each page sets `$title`, `$description`, `$canonical`, `$ogImage` in an `@php` block before `@extends`, and pushes JSON-LD via `@push('schema')`. Match that pattern for new pages.
- **Styling:** Tailwind v4 via Vite. Theme tokens are in `resources/css/app.css` (`--color-katt-*`). Prefer utility classes.
- **DB:** SQLite for dev/test; production can switch to MySQL via `.env` only, no code changes.

## Comic metadata fields

`title`, `alt_text` (accessibility + SEO, required), `caption` (transcript shown under the strip), `description` (meta description; falls back to caption → alt_text). The importer fills sensible placeholders; a future admin/edit flow should let these be edited.

## beehiiv integration (hybrid newsletter)

The site runs fine without beehiiv. When the user is ready, there are three hook points, implement in this order:

1. **Signup capture (easiest).** `resources/views/components/newsletter-signup.blade.php` renders when `config('comics.beehiiv.embed_url')` is set (`BEEHIIV_EMBED_URL` in `.env`). It's already placed on the reader and empty-state pages. Swap the iframe for beehiiv's real embed, or build a custom form that POSTs to beehiiv's subscribe API.
2. **Auto-email each new strip (no code).** Point a beehiiv **RSS-to-email** automation at `/feed` (already implemented in `FeedController` + `resources/views/feed/rss.blade.php`, includes the strip image and a link back). This is the recommended distribution path.
3. **Monetization.** Lives entirely in beehiiv (ads, paid subscriptions). If gating early/bonus strips, add a `is_premium` flag to `comics` and check beehiiv subscription status, but keep the canonical strip on-site for SEO.

Important: the apex domain `startupkatt.com` is THIS Laravel app. beehiiv should live on a subdomain (e.g. `news.startupkatt.com`) or its free beehiiv subdomain. Do not move the apex to beehiiv.

## Things that are intentionally NOT here (good next tasks)

- ~~No admin/upload UI~~: a metadata editor now lives at `/admin` (edit `title`/`alt_text`/`caption`/`description` + override `published_at`), gated by a single HTTP Basic credential (`ADMIN_USERNAME`/`ADMIN_PASSWORD`, see `config/comics.php`). Publishing itself is still folder-drop by design; there's deliberately no upload form. A future add could let admins upload art or manage the schedule visually.
- No general auth/users: the admin is a single shared HTTP Basic gate, nothing more.
- ~~No `og-default.png`~~: a 1200×630 fallback ships at `public/og-default.png`.
- No image optimization/thumbnails; could generate WebP + responsive sizes on import.
- **Reactions are a growth loop, not analytics.** Readers tap one of the `config('comics.reactions')` options per strip (changeable/toggleable, no login). Tallies are denormalised counts in the `reactions` table. **Anti-spam:** the count only ever moves once per IP per comic: a hashed-IP row in `reaction_votes` (unique on `comic_id, ip_hash`) is the *sole authority* for counting, so clearing the cookie, incognito, or scripting can't inflate it; the route is also rate-limited (`throttle:20,1`). The `sk_votes` cookie is now **UI-only** (mirrors your pick so the widget highlights instantly; capped to the last 100 strips) and plays no part in counting. Trade-off: shared IPs (office WiFi) share a vote, so counts lean conservative, the right failure mode for social proof. Future strips can't be voted on (POST 404s) and the widget is hidden in preview.
- **The leaderboard is the content/SEO loop** the reactions feed. `/top` ranks published strips by total reactions; `/top/{reaction}` ranks by a single reaction (e.g. `/top/unhinged`), headlined by that reaction's `superlative` from config. Ranking queries are `Comic::topOverall()` / `Comic::topByReaction()` (strips with zero reactions are excluded). The page is built to the `seo-aeo-content` skill: descriptive H1, ItemList + FAQPage JSON-LD, a visible FAQ, and a "last updated" line; it `noindex`es itself when fewer than 3 strips are ranked (no thin pages), and the sitemap only lists leaderboards that clear that bar. The **top-strip toast** (`x-top-strip-toast`, on the reader) teases the #1 strip after ~8s, once per session (sessionStorage), dismissible, and never teases the strip you're already on; its CTA links to `/top`.

## Commands

```bash
composer install && npm install
php artisan migrate
php artisan storage:link
php artisan comics:import           # --dry-run to preview
php artisan test
npm run dev
```
