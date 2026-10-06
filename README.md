# miguelangelgarcia.com

The personal portfolio of Miguel Angel Garcia, Senior Full-Stack Engineer. It's a one-page site with an AI assistant in the hero that answers visitors' questions about my work, grounded only in notes I've written for it.

**Live:** [miguelangelgarcia.com](https://miguelangelgarcia.com)

## Highlights

- **Server-rendered for every crawler.** The public page is Laravel Blade, so search engines and no-JS AI crawlers get the full content in the HTML. It ships JSON-LD (`Person` + `WebSite`), a sitemap, a `robots.txt` that welcomes AI crawlers, and an [`llms.txt`](public/llms.txt) summary.
- **"Ask about me" assistant.** A RAG-style assistant built on the Claude API. Questions are classified, matched against a curated knowledge base, and answered as a streamed response. If something isn't documented, it says so instead of guessing. It's a small React island on top of a server-rendered fallback, so the page still works without JavaScript.
- **Admin.** An owner-only admin at `/admin` (Inertia + React + Mantine) for managing knowledge entries and reviewing the questions visitors ask, including the ones the assistant couldn't answer.
- **Contact form.** Submitted with fetch, falls back to a normal POST, delivered through Postmark, and screened with reCAPTCHA v3.
- **No Node.js in production.** Node runs only at build time with Vite. There's no server-side rendering.

## Stack

| Area      | Tools                                                              |
| --------- | ------------------------------------------------------------------ |
| Backend   | PHP 8.3+, Laravel 13                                               |
| Frontend  | Blade, Tailwind CSS v4, vanilla JS, React 19 + TypeScript (islands) |
| Admin     | Inertia 2, React, Mantine (client-rendered only)                   |
| AI        | Anthropic Claude API (classification + answers)                   |
| Database  | SQLite locally, MySQL in production                                |
| Testing   | Pest, Pint, `tsc`                                                  |
| Delivery  | Vite, GitHub Actions → Bluehost                                    |

## Getting started

Requirements: PHP 8.3+, Composer, and Node 22+.

```bash
composer setup    # install deps, create .env, generate key, migrate, build assets
php artisan admin:create you@example.com       # create the admin account
php artisan db:seed --class=AiKnowledgeSeeder  # starter knowledge entries
composer dev      # app server, queue, logs, and Vite
```

I serve it locally with [Laravel Herd](https://herd.laravel.com) at `https://miguelgarcia.test`, which replaces `php artisan serve`. Run `npm run dev` alongside it for hot reloading.

### Configuration

Copy `.env.example` to `.env`. Every integration is optional, and the site runs without any of them:

| Variable                                    | Purpose                                                                |
| ------------------------------------------- | ---------------------------------------------------------------------- |
| `ANTHROPIC_API_KEY`                         | Powers the assistant. Set `AI_ENABLED=false` to hide it entirely.      |
| `POSTMARK_API_KEY`, `CONTACT_TO_ADDRESS`    | Contact form delivery (`MAIL_MAILER=log` writes emails to the log locally). |
| `RECAPTCHA_SITE_KEY`, `RECAPTCHA_SECRET_KEY` | Spam scoring on the contact form.                                     |
| `GOOGLE_ANALYTICS_ID`                       | Google Analytics 4. Set it in production only.                         |

Site content, including the name, projects, experience, and stack, lives in [`config/portfolio.php`](config/portfolio.php). The assistant's settings are in [`config/ai.php`](config/ai.php).

## Testing

```bash
php artisan test --compact           # Pest suite (the Claude API is faked; tests never call it)
vendor/bin/pint --dirty              # format PHP
npx tsc --noEmit                     # type-check the React code
```

## Deployment

Every push to `main` runs [`.github/workflows/deploy.yml`](.github/workflows/deploy.yml), which:

1. Installs Composer dependencies.
2. Builds the frontend.
3. Syncs the files to Bluehost over SSH with rsync.
4. Runs migrations and refreshes the Laravel caches.

You can also run it manually from the Actions tab, with a dry-run option.

Production caches its config, so after editing the server's `.env`, run `php artisan config:cache`.
