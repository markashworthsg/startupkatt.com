---
name: seo-aeo-content
version: 2.0.0
description: |
  SEO and AEO (Answer Engine Optimization) strategy for 2026, applied to the
  Startup Katt webcomic. Use when writing comic metadata (title, alt_text,
  caption/transcript, description), companion posts, landing/about pages, or any
  page meant to drive organic traffic or AI citations. Grounded in HubSpot's 2026
  research across 10,000 URLs and 8 AI engines (AI Overviews, Gemini, Perplexity,
  ChatGPT, SearchGPT, Grok, Google AI Mode, Copilot), which identified the on-page
  signals that actually predict citations.
allowed-tools:
  - Read
  - Write
  - Edit
  - Grep
  - Glob
---

# SEO + AEO Content Strategy (2026)

Apply these rules to any Startup Katt page that should rank in search or get cited by AI. The whole site is built around per-comic SEO/AEO URLs (`/comic/{slug}`), so this is core, not optional.

## The Core Shift

AI Overviews answer broad informational queries directly in search results. Generic content gets zero clicks even at position 16. Only specific, original, hard-to-replicate content still earns traffic.

**What died:** Generic content. "What is X" articles. Broad keyword targeting. Thin pages that any AI can summarize away.

**What works:** Original creative work, specific and searchable situations, real transcripts, and pages an AI cannot reproduce because the content only exists here.

## Content Rules

### 1. Kill Generic, Go Specific

A webcomic's SEO advantage is specificity. People search for the exact, oddly precise moment they're living.

Bad slug/title: "funny startup comic"
Good: "the founder who raised a seed round to avoid talking to a customer"

Bad: "tech humor"
Good: "when your cofounder marks the standup as optional"

If AI can satisfy the query without your strip, the page won't pull traffic. Aim each strip's metadata at a situation a real person would type into search.

### 2. Lead With Original Content

The strip itself is the moat. AI can summarize an explainer; it can't reproduce your art or your specific joke. Make that originality legible to crawlers:
- A real transcript in `caption` (the spoken/visible text of the strip), not a vague summary.
- Specific, descriptive `alt_text` that names what's actually happening in the panel.
- A `description` that captures the exact situation, not "a funny comic about startups."

Original art + a real transcript is content that gets cited and linked, not summarized away.

### 3. Structure for AI Extraction

- Use clear H2/H3 headings that are questions or specific topics (on any companion/about pages).
- Short paragraphs (2-3 sentences max).
- Put the explicit answer near the top of each section (inverted pyramid).
- Include specific, concrete detail.
- Use FAQ format where natural (e.g. an about/FAQ page, or "what is this comic" answers).
- Add schema markup. Comic pages already push JSON-LD; use Article/CreativeWork for strips and FAQ/HowTo where appropriate.

### 4. Target Bottom-of-Discovery Intent

Write for people close to the moment of finding and following something, not idle browsers.

High intent: "best webcomics about startup life", "comics like Dilbert but for tech founders", "startup cat comic" (they want to find and follow one).
Low intent: "what is a webcomic" (AI answers this, zero clicks).

Every page should make the next action obvious:
- Read the latest strip.
- Browse the archive (first / prev / next / latest).
- Subscribe to the newsletter or RSS so the strip comes to them.

### 5. Build for Brand Search

The goal is not just ranking. The goal is making people search "startup katt" by name. Every page should:
- Reference the comic and character by name (and lean into the running gag: he's "Startup Katt" but everyone calls him "Startup Cat").
- Have a consistent, identifiable voice readers recognize.
- Give enough that people bookmark, subscribe, and come back.

### 6. Content Types That Survive AI

Prioritize formats AI can't fully answer (in order of resilience):
1. **Original creative work** (the strips themselves) - AI can't generate your comic.
2. **Real transcripts and archives** - the exact words and the browsable back catalogue.
3. **Specific situational humor** tied to searchable real-life moments.
4. **Behind-the-strip / opinion posts** with a clear point of view - AI gives neutral summaries; readers want a take.
5. **Recommendation / list pages** ("startup webcomics worth following") with genuine perspective.

Avoid:
- "What is X" definitions.
- Generic best-practices lists.
- Anything that could be written by anyone with Google access.

### 7. AEO: Answer Engine Optimization

The AEO playbook differs from SEO. HubSpot's 2026 analysis of 10,000 URLs across 8 AI engines (AI Overview, Gemini, Perplexity, ChatGPT, SearchGPT, Grok, Google AI Mode, Copilot) showed which on-page signals actually predict citations.

**Backlinks don't predict AI visibility.** Correlation between backlink count and AI citations is essentially zero (-0.04 to +0.06 across all 8 engines). The on-page signals that DO correlate:

| Signal | Citation lift |
|---|---|
| Question-style H2/H3 headings | +28 AI Overviews, +19 Gemini |
| FAQ schema (JSON-LD) | +24 AI Overviews |
| 20+ outbound external links to authoritative sources | +19 AI Overviews |
| On-page FAQ section (visible, matched to schema) | +18 AI Overviews |
| Visible "last updated: [date]" line | +8 AI Overviews, +6 Perplexity, +5 Gemini |
| Descriptive H1 (specific, not generic) | meaningful lift across engines |
| Statistics, block quotes, TLDR / summary blocks | meaningful additional lift |

Treat the table above as the working AEO checklist for any text-heavy page (about, FAQ, companion posts). For strip pages, the equivalent wins are a descriptive title/H1, a real transcript, and accurate alt text.

**Recency matters quantifiably.** AI-cited content is ~25% more recent than Google-cited content (909 vs 1,047 average days since last updated). A visible "last updated: [date]" line is one of the highest-leverage 5-minute fixes available.

**Rules to apply:**
- Use question-style H2s and H3s (literally phrased as questions) on text pages.
- Add FAQ schema AND a visible FAQ section.
- Show a date prominently (publish date on strips, "last updated" on evergreen pages).
- Include external links to authoritative sources where it makes sense on companion posts.
- Lead each section with an extractable TLDR or quote.
- Own a narrow niche: be the comic for startup/founder life.
- Write extractable sentences, not flowery prose.

**FAQ schema, but different FAQs per page.** Google deprecated FAQ schema for SEO (no longer shown in SERPs) but it still hits hard for AEO. Don't copy-paste the same questions across pages; match FAQs to each page's specific intent. Identical content across pages gets noticed and discounted by LLMs.

### 7a. Where Discovery Citations Happen

AI engines cite most heavily at the "which one should I pick / follow" stage. For a webcomic that maps to recommendation and comparison queries, not definitions:
- Recommendation pages ("best webcomics about startup life", "funny comics for founders").
- "Comics like [X]" comparison pages.
- A strong about/FAQ page that answers "what is Startup Katt" with personality.

Definitions and "what is a webcomic" are vanity intent in 2026. Being the comic that gets named when someone asks an AI for "a good startup webcomic" is the whole game.

### 7b. Off-Site Authority (Reddit, LinkedIn, social video)

For "best X" and recommendation queries, Reddit + LinkedIn + social video dominate AI citations. If you're not present on at least two, you're leaving citations on the table.

- **Reddit**: an aged account genuinely participating in startup/founder and webcomic subs, sharing strips where they actually fit (no spam). LLMs heavily cite Reddit for "best X" threads.
- **LinkedIn**: post strips as native image posts to founders. Treat it as a publishing surface; startup humor performs there.
- **Instagram / X / TikTok / Shorts**: comics are native to these. Each post is also a discovery surface that links back to the canonical strip on-site.

Keep the canonical, indexable strip on `startupkatt.com` for SEO; treat social as distribution, not the home.

### 8. SEO Technical Checklist

Every page must have (the repo's existing comic pages already follow most of this):
- [ ] Title tag under 60 chars, specific and descriptive
- [ ] Meta description 50-160 chars, gives a reason to click
- [ ] Canonical URL set
- [ ] OG and Twitter card tags with the strip image (with an `og-default.png` fallback)
- [ ] Article / CreativeWork schema (JSON-LD)
- [ ] FAQ schema on pages with Q&A sections
- [ ] Internal links to latest, archive, and related strips
- [ ] Alt text on every image (already required on comics)
- [ ] URL slug short and keyword-rich (the per-comic slug)

### 9. Measuring Success (New Metrics)

Don't just track rankings. Track:
- **Prompts, not keywords.** Find the actual questions people type into ChatGPT / Perplexity (e.g. "good startup webcomic"). Track whether Startup Katt shows up. This is the new ranking metric.
- **AI citations** (does ChatGPT/Perplexity name or link the comic? by prompt and engine).
- **Brand search volume** (are more people searching "startup katt" / "startup cat comic"?).
- **Return readers and newsletter signups** (did the page turn a visitor into a subscriber?).
- **Archive depth** (did they read more than one strip?).
- **Social shares** (did readers share a strip voluntarily?).

Rankings alone mean nothing in 2026.

### 10. Distribution > Creation

Drawing the strip is part of the work. Getting it in front of people is most of it.
- Post each strip on X / LinkedIn / Instagram with a hook, not "new comic".
- Share where the situation actually resonates (relevant subreddits, founder communities).
- Use the RSS-to-email newsletter so every strip reaches subscribers automatically.
- Cross-link new strips from the archive and high-traffic pages.
