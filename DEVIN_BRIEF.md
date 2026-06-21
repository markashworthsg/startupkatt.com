# Devin kickoff — Startup Katt

## Context
Startup Katt is a Laravel 12 site that hosts a daily AI-generated webcomic. The project is already scaffolded. **Read `CLAUDE.md` in the repo root first** — it's the source of truth for architecture, the scheduling rule, and conventions. Don't restate it; follow it.

Repo: `~/Sites/startupkatt.com` (host it on the same server as growthpigeon.com).

## Environment / hosting (must match)
- PHP **8.2+**, Laravel **12** — identical to the existing `growthpigeon` site on this server. Use that site's PHP-FPM pool/vhost as the template.
- Do **not** target pcosmealplanner.com's setup (PHP 7.1 / Laravel 5.7) — that's a legacy site on the same box.
- SQLite for local/dev; production can switch to MySQL via `.env` only (no code changes).
- Apex domain `startupkatt.com` = this Laravel app. beehiiv, if used, lives on a subdomain — never move the apex.

## Step 0 — verify the baseline before changing anything
1. `composer install && npm install`
2. `cp .env.example .env && php artisan key:generate`
3. `php artisan migrate && php artisan storage:link`
4. `php artisan test` — **all tests must pass.** If any fail, fix the cause before doing feature work and report what was wrong.
5. `git init` (if needed) and commit the scaffold as the baseline before further changes.

## Tasks (in priority order)
1. **Confirm it runs.** `npm run dev` + `php artisan serve`, load `/`, drop a test image in `storage/app/comics/incoming/`, run `php artisan comics:import`, confirm it appears and prev/next/archive/feed/sitemap all work.
2. **Add `public/og-default.png`** — a 1200×630 social fallback image for pages without a comic image.
3. **Build a minimal admin page** to edit a comic's `title`, `alt_text`, `caption`, `description`, and override `published_at`. Protect it with simple auth (Laravel Breeze or a single gate — keep it light). This is the highest-value gap.

## Constraints / do-not-break
- The scheduling logic in `app/Console/Commands/ImportComics.php` and `Comic::nextSlot` semantics are covered by `tests/Feature/ComicScheduleTest.php`. If you change scheduling behaviour, update those tests and keep them green.
- Public pages must only show comics where `published_at <= today` (use the `published()` scope). Future-dated comics must keep returning 404.
- Preserve the per-comic SEO/AEO markup (title, meta description, canonical, OG/Twitter, `ComicStory` JSON-LD) on every comic page.
- Keep the beehiiv hooks intact (`newsletter-signup` component + `/feed` RSS); they're config-gated and safe to leave dormant.

## Acceptance criteria
- `php artisan test` green, including any tests you add for the admin page.
- A fresh clone can go from `composer install` to a running site following `README.md` with no extra steps.
- Admin can edit metadata and change a comic's date; the change is reflected on the public page and the date stays unique (one comic per day).
- No regression in navigation, SEO tags, feed, or sitemap.

## Voice note
The cat is "Startup Katt" but everyone calls him "Startup Cat" — keep that running gag in any user-facing copy.
