---
name: belt-framework
version: 1.0.0
description: |
  The BELT framework for evaluating SaaS product durability. Use when:
  - Writing clarity maps (every map must include a BELT analysis section)
  - Analyzing any SaaS product's positioning or retention
  - Proposing or evaluating new Growth Pigeon features (run BELT against the feature to check if it builds on existing behavior, solves an enduring problem, creates lock-ins, and avoids transient distractions)
  - Advising whether a product will retain users
  BELT = Behavior, Enduring problems, Lock-ins, Transient distractions.
allowed-tools:
  - Read
  - Write
  - Edit
  - Grep
  - Glob
  - AskUserQuestion
---

# BELT Framework: The SaaS Durability Test

BELT is Growth Pigeon's proprietary framework for predicting whether a SaaS product will retain users or die. Every clarity map includes a BELT analysis. The framework tests four things, in order.

## The Four Questions

### B: Behavior
**Question: Are you building on an existing behavior, or trying to create a new one?**

Most apps fail because they try to innovate around a behavior that doesn't exist. Changing your own habits is hard. Changing thousands of users' habits is nearly impossible.

**The test:** Can you describe what your users already do today (without your product) that your product improves? If you can't, you have a behavior problem.

**Pass examples:**
- Uber: people already hailed cabs. Uber made it easier. Innovation on top of existing behavior.
- Spark (clarity map example): people already scroll something every day. Spark replaces low-signal scrolling with a higher-signal ritual that still feels light.

**Fail examples:**
- Meal logging apps: people don't currently log meals. You're asking them to build an entirely new behavior from scratch.
- Any product that requires users to "remember to check" it without attaching to something they already do.

**When writing BELT for a clarity map:** Identify the existing behavior the product attaches to. Be specific. "People already use spreadsheets for this" is better than "people already track data." If the product requires a new behavior, say so directly and flag the risk.

---

### E: Enduring Problem
**Question: Does this problem go away once solved, or does it keep coming back?**

Figuring out what enduring problem you're solving for your ICP is the fastest way out of the product-market fit pain cave.

**The test:** If your user perfectly solves their problem today, do they need you again tomorrow?

**Enduring (good for SaaS):**
- Managing team work and projects: never stops
- Purpose drift in modern life (Spark example): as long as people are busy and overloaded, they will struggle to maintain values-aligned behavior without a ritual
- SaaS positioning: your market, competitors, and messaging constantly evolve

**Transient (bad for SaaS):**
- Wedding planning: painful, but it goes away
- Setting up a brand pyramid: once you have it, you have it
- Filing taxes: annual, not daily

**When writing BELT for a clarity map:** Reframe the problem away from the obvious. Spark's enduring problem is not "charity discovery." It is "purpose drift in modern life." Find the deeper behavioral problem that persists indefinitely.

---

### L: Lock-ins
**Question: What keeps users stuck in their current solution, and how will you overcome it?**

This is the brutal truth most founders don't want to hear. If you can't get people out of their existing solutions, your product fails. Doesn't matter if their tools suck. Doesn't matter if yours are better. Lock-ins beat features every time.

**Types of lock-ins to analyze:**

1. **Data lock-in:** Years of history, content, or configuration trapped in the old tool
2. **Workflow lock-in:** Teams have built processes around the existing tool
3. **Identity/psychological lock-in:** "I'm a Notion person" or streak-based attachment
4. **Integration lock-in:** Other tools depend on the current solution
5. **Social lock-in:** The team/community is already there

**When writing BELT for a clarity map:** Identify both:
- Lock-ins that trap users in COMPETING solutions (barriers to switching TO this product)
- Lock-ins this product should BUILD (barriers to switching AWAY from this product)

Spark example of lock-ins to build:
- Streak identity: "I do this every day"
- Impact memory: a durable record of what you learned and enabled
- Curator trust: belief that Spark's causes are vetted and real
- Personal narrative: a timeline that proves you are not drifting

Note: the best lock-ins are psychological and historical, not feature-based. If switching feels emotionally costly, you win.

---

### T: Transient Distractions
**Question: Are you chasing temporary pain points that won't drive long-term retention?**

When you combine transient problems with behaviors that don't exist and ignore the lock-ins keeping users stuck, you get products that might get initial sign-ups but will never retain users. The math just doesn't work.

**The test:** For each feature or problem you're solving, ask: will this matter to the user in 6 months? If not, it's a transient distraction.

**When writing BELT for a clarity map:** List the things the product should NOT chase. Be specific and prescriptive. This section is about protecting the core by saying no.

Spark example of transient distractions to avoid:
- Too many causes and infinite browsing
- Too much data, too many charts, too much control
- Social gamification that feels performative
- AI features that dilute trust

The key insight: "This is a ritual. Rituals die when they become complicated."

---

## How to Write a BELT Section in a Clarity Map

### Structure
```
## BELT Framework Analysis

BELT is a durability test used in Growth Pigeon clarity maps: Behavior, Enduring problem, Lock-ins, Transient distractions.

### Behavior
[2-3 paragraphs analyzing the existing behavior the product attaches to]

### Enduring Problem
[2-3 paragraphs on whether the core problem persists indefinitely]

### Lock-ins
[List of lock-ins the product should build, with explanations]
[Final sentence: "If [product] can make the user feel [emotional state], switching becomes emotionally costly."]

### Transient Distractions
[Bullet list of things the product should NOT chase]
[Final sentence that crystallizes why these would break the product]
```

### Tone
- Direct and prescriptive, not academic
- Use concrete examples from the product being analyzed
- Be willing to say the product is weak on a dimension
- Short paragraphs, no hedging
- Frame lock-ins as psychological, not technical

### The Verdict
After the BELT analysis, the clarity map should make clear whether the product passes or fails each dimension. A product that fails on Behavior or Enduring Problem has a fundamental structural issue. A product that fails on Lock-ins or Transient Distractions has a strategic issue that can be fixed.

---

## BELT as a Quick Scoring Tool

When evaluating a product quickly (not a full clarity map), run through these four questions:

1. **B:** What existing behavior does this attach to? (If none: red flag)
2. **E:** Will users need this again next week? Next month? (If no: not SaaS-viable)
3. **L:** What would make it painful to leave? (If nothing: no moat)
4. **T:** What should this product refuse to build? (If unclear: scope creep risk)

A product that scores well on all four has the foundation for retention. A product that fails on even one is at risk.
