# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Miguel Angel Garcia's personal portfolio (miguelgarcia.site) — a **Laravel 13** app whose public page is deliberately **server-rendered Blade** so every crawler — including no-JS AI crawlers — gets full HTML. **The production server has no Node.js**: Node is used at build time only (Vite). React is allowed where it is compiled ahead of time and does not carry the public content:

- the public page stays Blade; its only React is the "Ask about me" island (`resources/js/ask/`), which enhances a server-rendered fallback
- the admin at `/admin` is Inertia 2 + React + Mantine, client-rendered only (`INERTIA_SSR_ENABLED=false`; never enable SSR)

Do not move public page content into React, and do not add runtime SSR.

Local dev is served by **Laravel Herd** at `https://miguelgarcia.test` — no `artisan serve` needed. Run `npm run dev` alongside for HMR, or `npm run build` for production assets.

## Commands

```bash
php artisan test --compact                     # Pest test suite
php artisan test --compact --filter=Contact    # single test file/name
vendor/bin/pint --dirty --format agent         # format changed PHP before finalizing
npx tsc --noEmit                               # type-check the React code
npm run build                                  # production assets (also bundles Bunny fonts)
composer dev                                   # server + queue + logs + vite, concurrently
php artisan admin:create you@example.com       # create/reset the single admin account
php artisan db:seed --class=AiKnowledgeSeeder  # starter knowledge entries (skips existing slugs)
```

Tests use Pest (converted from the skeleton's PHPUnit — keep new tests in Pest style).

## Architecture

**One page, three view directories** (`resources/views/`):
- `layouts/app.blade.php` — the layout; owns the entire SEO head: title/description via `@yield`, canonical, OG/Twitter tags, `@stack('head')`, `@fonts` (Bunny fonts via Vite plugin), `@vite`.
- `pages/home.blade.php` — composes the portfolio from components; builds JSON-LD (`Person` + `WebSite`) and pushes it to the head stack.
- `components/portfolio/*.blade.php` — anonymous components (nav, hero + `hero/{statement,split,editorial}` variants, about, projects, stack, contact, footer, image-slot, quote, section-intro). Hero variant is chosen by prop in `pages/home.blade.php`.

**Content lives in `config/portfolio.php`** — name, job title, description, `same_as` profile URLs (feeds JSON-LD), projects, experience, brands, and stack. Edit content there, not in templates.

**CSS**: `resources/css/app.css` — Tailwind v4 with the design tokens declared in `@theme` (colors like `bg-accent`, fonts `font-display`). Components use Tailwind utilities directly. `resources/css/admin.css` loads Mantine's stylesheet for the admin only.

**JS**: `resources/js/app.js` is vanilla scroll-reveal, in-page focus handling, and the contact form's fetch submit. It's progressive enhancement — the page must remain fully usable and visible with JS disabled.

## "Ask about me" assistant

A RAG-style assistant grounded only in the knowledge base managed at `/admin/ai-knowledge`. Flow (all in `app/Services/Ai/`): `PortfolioAiService` → `QuestionClassifier` (Claude Haiku, JSON schema, validated) → `KnowledgeRetriever` (`KeywordKnowledgeRetriever`: category/tag/keyword scoring in PHP, cached active entries) → `PromptBuilder` → `LlmClient` (`AnthropicLlmClient`, the only provider-specific class) → streamed as server-sent events by `PortfolioAiController` on `POST /ask`. Every question is logged to `ai_question_logs` without visitor PII.

- Settings, model ids, limits, pricing, categories and suggested questions: `config/ai.php`; the API key is `ANTHROPIC_API_KEY`.
- Entries carry one to five free-form `categories` (JSON) plus tags; STAR stories are `kind = star`, not a category. Only `is_active` entries reach the model. An ABOUT_ME question with no matching entry returns the fixed "not documented" answer without a model call.
- `LlmClient` and `KnowledgeRetriever` are the swap points (an embedding retriever is the planned phase 2). Tests bind `Tests\Support\FakeLlmClient`; never call the real API from tests.
- Frontend: `resources/js/ask/` (React island, Tailwind classes, no Mantine) mounted into `components/portfolio/ask.blade.php`, which renders the no-JS fallback. Admin pages: `resources/js/admin/pages/**` (Inertia page names map to that folder — see `config/inertia.php`).
- Auth is a single owner account (`php artisan admin:create`); there is no registration.

**Contact form** is server-side: POST `/contact` → `ContactController::send` + `ContactRequest` (custom messages matching the design copy; `$redirect = '/#contact'` keeps validation errors anchored to the section). Submissions are emailed via Postmark and scored with reCAPTCHA v3 (`App\Services\Recaptcha`).

**SEO/GEO surface** (the reason this app exists in this form):
- JSON-LD in `pages/home.blade.php` — built inside `@php` because Blade otherwise compiles the `@context` key as a Blade directive (this was a real bug; don't move schema arrays into template text)
- `public/robots.txt` — explicitly allows AI crawlers (GPTBot, ClaudeBot, PerplexityBot, etc.); keep them allowed
- `public/llms.txt` — AI-readable site summary; keep in sync when content changes
- `/sitemap.xml` — route in `routes/web.php`; update `lastmod` when content changes
