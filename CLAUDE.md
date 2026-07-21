# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

Miguel Angel Garcia's personal portfolio (miguelgarcia.site) — a deliberately **Blade-only Laravel 13** app. It was rebuilt from a React/Inertia starter kit (recoverable at commit `24ea260`) specifically so every crawler — including no-JS AI crawlers — gets full server-rendered HTML. **Do not introduce Inertia, React, or runtime SSR; the production server has no Node.js.** Node is used at build time only (Vite).

Local dev is served by **Laravel Herd** at `https://miguelgarcia.test` — no `artisan serve` needed. Run `npm run dev` alongside for HMR, or `npm run build` for production assets.

## Commands

```bash
php artisan test --compact                     # Pest test suite
php artisan test --compact --filter=Contact    # single test file/name
vendor/bin/pint --dirty --format agent         # format changed PHP before finalizing
npm run build                                  # production assets (also bundles Bunny fonts)
composer dev                                   # server + queue + logs + vite, concurrently
```

Tests use Pest (converted from the skeleton's PHPUnit — keep new tests in Pest style).

## Architecture

**One page, three view directories** (`resources/views/`):
- `layouts/app.blade.php` — the layout; owns the entire SEO head: title/description via `@yield`, canonical, OG/Twitter tags, `@stack('head')`, `@fonts` (Bunny fonts via Vite plugin), `@vite`.
- `pages/home.blade.php` — composes the portfolio from components; builds JSON-LD (`Person` + `WebSite`) and pushes it to the head stack.
- `components/portfolio/*.blade.php` — anonymous components (nav, hero + `hero/{statement,split,editorial}` variants, about, projects, stack, contact, footer, image-slot, quote, section-intro). Hero variant is chosen by prop in `pages/home.blade.php`.

**Content lives in `config/portfolio.php`** — name, job title, description, `same_as` profile URLs (feeds JSON-LD), projects, and stack. Edit content there, not in templates.

**CSS**: `resources/css/portfolio/` — one file per section, imported by `portfolio.css` (theme tokens + shared `pf-*` primitives), which `app.css` imports next to Tailwind v4. Everything is scoped under `.portfolio`; components use `pf-*` BEM-style classes, not Tailwind utilities. Careful with selector specificity: a bare `.portfolio a` rule outranks single-class selectors like `.pf-btn--primary` — generic element rules are scoped to `p a` for this reason.

**JS**: `resources/js/app.js` is ~30 lines of vanilla scroll-reveal (IntersectionObserver). It's progressive enhancement — the page must remain fully usable and visible with JS disabled.

**Contact form** is server-side: POST `/contact` → `ContactController::send` + `ContactRequest` (custom messages matching the design copy; `$redirect = '/#contact'` keeps validation errors anchored to the section). Submissions are currently only logged — a Mailable is a pending TODO.

**SEO/GEO surface** (the reason this app exists in this form):
- JSON-LD in `pages/home.blade.php` — built inside `@php` because Blade otherwise compiles the `@context` key as a Blade directive (this was a real bug; don't move schema arrays into template text)
- `public/robots.txt` — explicitly allows AI crawlers (GPTBot, ClaudeBot, PerplexityBot, etc.); keep them allowed
- `public/llms.txt` — AI-readable site summary; keep in sync when content changes
- `/sitemap.xml` — route in `routes/web.php`; update `lastmod` when content changes
