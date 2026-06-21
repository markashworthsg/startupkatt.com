---
name: seo-aeo-content
version: 1.1.0
description: |
  SEO and AEO (Answer Engine Optimization) content strategy for 2026.
  Use when writing blog posts, articles, landing pages, or any content
  intended to drive organic traffic or AI citations. Grounded in HubSpot's
  2026 research across 10,000 URLs and 8 AI engines (AI Overviews, Gemini,
  Perplexity, ChatGPT, SearchGPT, Grok, Google AI Mode, Copilot), which
  identified the on-page signals that actually predict citations and the
  buyer-journey stages where citations concentrate.
allowed-tools:
  - Read
  - Write
  - Edit
  - Grep
  - Glob
---

# SEO + AEO Content Strategy (2026)

Apply these rules when writing any content for Startup Katt that should rank in search or get cited by AI.

## The Core Shift

AI Overviews answer broad informational queries directly in search results. Generic content gets zero clicks even at position 16. Only specific, actionable, data-rich content still drives traffic.

**What died:** Generic informational content. "What is X" articles. Broad keyword targeting. Thin programmatic pages.

**What works:** Specific product comparisons. Original data and research. Interactive tools. Bottom-funnel content. Restaurant-guide-level specificity. Exact instructions with real numbers.

## Content Rules

### 1. Kill Generic, Go Specific

Bad: "How to improve your SaaS positioning"
Good: "Your SaaS homepage scored 34/100. Here's exactly what's wrong with your headline."

Bad: "What is a value proposition"
Good: "I graded 50 SaaS homepages. 80% fail the 5-second clarity test. Here's the pattern."

If AI can answer the query in 2 sentences, don't write the article. Write about things AI needs YOUR data to answer.

### 2. Lead With Original Data

Every article should contain at least one of:
- Proprietary data from Startup Katt tools (grader scores, clarity map patterns, engagement data)
- Specific before/after examples with real URLs
- Aggregated patterns from real analyses ("we graded 100 SaaS homepages and found...")
- Screenshots showing real results

AI can't generate your data. Data-rich content gets cited, not summarized away.

### 3. Structure for AI Extraction

- Use clear H2/H3 headings that are questions or specific topics
- Short paragraphs (2-3 sentences max)
- Explicit answers near the top of each section (inverted pyramid)
- Include specific numbers, percentages, and examples
- Use FAQ format where natural
- Add schema markup (Article, FAQ, HowTo as appropriate)

### 4. Target Bottom-Funnel Intent

Write for people ready to act, not people browsing.

Bottom-funnel: "how to fix my SaaS homepage headline" (they know they have a problem)
Top-funnel: "what is SaaS marketing" (AI answers this, zero clicks)

Every article should have a clear action the reader can take immediately:
- Use the positioning grader
- Get a clarity map
- Apply a specific framework to their site

### 5. Build for Brand Search

The goal is not just ranking. The goal is making people search "startup katt" by name. Every article should:
- Reference Startup Katt tools by name
- Include unique frameworks (BELT, clarity map methodology)
- Have a perspective that's identifiably Mark's voice
- Give enough value that readers bookmark and return

### 6. Content Types That Survive AI

Prioritize these formats (in order of AI-resistance):
1. **Interactive tool content** (positioning grader, calculators) - AI can't replicate interaction
2. **Original research** ("we analyzed 100 homepages...") - AI can't generate your data
3. **Specific teardowns** (clarity maps with real screenshots) - requires proprietary analysis
4. **Implementation guides** ("how to rewrite your H1 using BELT") - step-by-step with examples
5. **Product comparisons** with real experience - nuance AI can't capture
6. **Opinion/analysis** with a clear point of view - AI gives neutral summaries, readers want takes

Avoid these (AI answers them fully):
- "What is X" definitions
- Generic best practices lists
- Broad category overviews
- Content that could be written by anyone with Google access

### 7. AEO: Answer Engine Optimization

The playbook for AEO is meaningfully different from SEO. HubSpot's 2026 analysis of 10,000 URLs across 8 AI engines (AI Overview, Gemini, Perplexity, ChatGPT, SearchGPT, Grok, Google AI Mode, Copilot) showed which on-page signals actually predict citations.

**Backlinks don't predict AI visibility.** Correlation between backlink count and AI citations is essentially zero (-0.04 to +0.06 across all 8 engines). Stop optimizing for the engine that's shrinking. The on-page signals that DO correlate with citations:

| Signal | Citation lift |
|---|---|
| Question-style H2/H3 headings | +28 AI Overviews, +19 Gemini |
| FAQ schema (JSON-LD) | +24 AI Overviews |
| 20+ outbound external links to authoritative sources | +19 AI Overviews |
| On-page FAQ section (visible, matched to schema) | +18 AI Overviews |
| Visible "last updated: [date]" line | +8 AI Overviews, +6 Perplexity, +5 Gemini |
| Descriptive H1 (specific, not generic) | meaningful lift across engines |
| Statistics, block quotes, TLDR / summary blocks | meaningful additional lift |

We covered the practical AI-citation correlation analysis (and what to do with the freed-up budget) in [Backlinks Do Not Predict AI Citations](/articles/backlinks-dont-predict-ai-citations). When writing new articles for Startup Katt, treat the table above as the working AEO checklist.

**Recency matters quantifiably.** AI-cited content is ~25% more recent than Google-cited content (909 vs 1,047 average days since last updated). Adding a visible "last updated: [date]" line is one of the highest-leverage 5-minute fixes available.

**Rules to apply:**
- Use question-style H2s and H3s (literally phrased as questions)
- Add FAQ schema AND a visible FAQ section
- Show "last updated" date prominently on every article
- Include 20+ external links to authoritative sources (this is more than SEO recommended)
- Lead each section with a TLDR or block quote that's extractable
- Update content regularly and re-stamp the date
- Be the authoritative source on a narrow topic — own the niche
- Write extractable sentences, not flowery prose

**FAQ schema on every page — but different FAQs per page.** Google deprecated FAQ schema for SEO (doesn't show in SERPs anymore). It still hits hard for AEO. The mistake people make: copy-pasting the same 5 questions across every page. Each page needs FAQs matched to that page's specific intent. Same content across pages = LLMs notice and discount it.

### 7a. Buyer Journey: Where Citations Actually Happen

HubSpot's data on where AI engines pull citations from across the buyer journey:

- **Solution Evaluation / Final Research: 55%** (the "which one should I pick" stage)
- **Solution Comparison: 19%** ("X vs Y")
- **Problem Exploration: 14%** ("why is my X broken")
- **Solution Education: 12%** ("what is X")

**Implication:** Stop writing top-of-funnel "what is" content. The citations are at the bottom of the funnel. Write:
- Comparison pages ("Best X for Y use case", "X vs Y")
- Evaluation guides with criteria, scoring, real recommendations
- Use case / persona pages (e.g. "How [Product] solves [specific challenge] for [industry]")
- Decision frameworks that help someone actually pick

Top of funnel is a vanity metric in 2026. Bottom of funnel is the whole game.

### 7b. Off-Site Authority (Reddit, LinkedIn, YouTube)

For high-intent questions at evaluation/comparison stages, Reddit + LinkedIn + YouTube dominate AI citations. If you're not present on at least two of those three, you're leaving citations on the table.

- **Reddit**: Aged account, no spamming, genuinely helpful answers in niche subs. LLMs heavily cite Reddit threads for "best X" and "X vs Y" queries.
- **LinkedIn**: Long-form POV posts in your category. Treat it as a publishing platform, not a network.
- **YouTube**: Question-style titles, dense walk-and-talk videos. Transcripts are crawled. AI engines cite YouTube heavily for tutorials and comparisons.

Third-party "top list" affiliate content also punches above its weight (one MarketsandMarkets CRM list was cited 85 times). Getting included in those lists is a high-leverage AEO play.

### 8. SEO Technical Checklist

Every article must have:
- [ ] Title tag under 60 chars, includes primary keyword
- [ ] Meta description 50-160 chars, compelling click reason
- [ ] Canonical URL set
- [ ] OG and Twitter card tags with custom image
- [ ] Article schema (JSON-LD)
- [ ] FAQ schema if article has Q&A sections
- [ ] Internal links to related tools and articles
- [ ] External links to authoritative sources (sparingly)
- [ ] Alt text on all images
- [ ] URL slug is short and keyword-rich

### 9. Measuring Success (New Metrics)

Don't just track rankings. Track:
- **Prompts, not keywords.** Find the actual questions your buyers type into ChatGPT / Perplexity. Track visibility per prompt, per location, per buyer-journey stage. This is the new ranking metric.
- **AI citations** (does ChatGPT/Perplexity cite your content? by prompt and engine)
- **Brand search volume** (are more people searching "startup katt"?)
- **Tool usage** (did the article drive grader/clarity map usage?)
- **Email captures** (did readers give their email?)
- **Social shares** (did readers share voluntarily?)
- **Time on page** (did they actually read it?)

Rankings alone mean nothing in 2026.

### 10. Distribution > Creation

Writing the article is 30% of the work. Distribution is 70%.
- Share on X with a hook (not just "new blog post")
- Post key insights to relevant communities
- Reference in clarity map replies
- Include in email sequences
- Cross-link from existing high-traffic pages
