<?php

namespace Database\Seeders;

use App\Models\AiKnowledgeEntry;
use Illuminate\Database\Seeder;

/**
 * Starter knowledge drawn only from what the site already says
 * (config/portfolio.php, the About section, public/llms.txt). Nothing here
 * is new information; it gives the assistant a working baseline until the
 * entries are refined in /admin/ai-knowledge.
 *
 * Safe to re-run: entries are matched by slug and existing ones are left
 * untouched so edits made in the admin are never overwritten.
 *
 *   php artisan db:seed --class=AiKnowledgeSeeder
 */
class AiKnowledgeSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->entries() as $entry) {
            AiKnowledgeEntry::firstOrCreate(['slug' => $entry['slug']], $entry);
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function entries(): array
    {
        return [
            // ---------------------------------------------------------- About
            [
                'slug' => 'who-miguel-is',
                'category' => 'About Me',
                'title' => 'Who Miguel is',
                'summary' => 'Senior full-stack engineer in Dallas–Fort Worth with 15+ years building web and mobile products, leading teams, and shipping AI-first software.',
                'content' => <<<'TEXT'
                Miguel Angel Garcia is a senior full-stack engineer based in Dallas–Fort Worth, Texas, with more than 15 years of experience (since 2008) building thoughtful, reliable digital products for web and mobile users.

                His career has evolved from designing and developing websites to architecting complex web applications, leading technical initiatives, and collaborating with engineers, designers, and business stakeholders to turn ideas into scalable solutions. He has worked in and led development teams, most notably as Director of Web Development at RealPage from 2015 to 2024.

                He brings a strong appreciation for interface design and user experience, along with a deep focus on architecture, maintainability, accessibility, and delivery. He cares not only about what a team builds but also how it collaborates, makes decisions, and consistently ships high-quality work.

                Over the past two years, AI-assisted development has become an important part of his day-to-day workflow. His personal motto is "Never. Stop. Learning."
                TEXT,
                'tags' => ['about', 'background', 'overview', 'full-stack', 'dallas', 'texas', 'experience', 'senior-engineer'],
                'importance' => 5,
            ],
            [
                'slug' => 'what-miguel-is-looking-for',
                'category' => 'Career Goals',
                'title' => 'What Miguel is looking for next',
                'summary' => 'Available for new opportunities: senior engineering roles that combine hands-on development with technical leadership, architecture, mentorship, and product thinking.',
                'content' => <<<'TEXT'
                Miguel is currently available for new opportunities. He is interested in senior engineering roles where he can combine hands-on development with technical leadership, architecture, mentorship, and product thinking.

                That may take the form of an engineering leadership position or a senior individual contributor role, depending on the team and the problems being solved. He is seeking a leadership role on an intelligent, creative team.

                The best way to reach him is the contact form on his portfolio site, or LinkedIn (linkedin.com/in/miguelgarcia7).
                TEXT,
                'tags' => ['availability', 'career-goals', 'leadership', 'senior', 'hiring', 'open-to-work', 'contact', 'why-hire'],
                'importance' => 5,
            ],
            [
                'slug' => 'how-miguel-approaches-building-products',
                'category' => 'Development Philosophy',
                'title' => 'How Miguel approaches building products',
                'summary' => 'Design-aware engineering with a focus on architecture, maintainability, accessibility, delivery, and how the team works together.',
                'content' => <<<'TEXT'
                Miguel approaches software with a strong appreciation for interface design and user experience, paired with a deep focus on architecture, maintainability, accessibility, and delivery.

                He cares not only about what a team builds, but also about how it collaborates, makes decisions, and consistently ships high-quality work. His background spans design, front-end, back-end, and team leadership, so he tends to think about a product end to end rather than one layer at a time.

                His own portfolio site reflects this: it is a deliberately server-rendered Laravel and Blade application so that every visitor and crawler receives complete HTML, it meets WCAG 2.1 AA accessibility guidelines, and it keeps JavaScript to progressive enhancement.
                TEXT,
                'tags' => ['philosophy', 'approach', 'architecture', 'maintainability', 'accessibility', 'ux', 'design', 'quality', 'problem-solving'],
                'importance' => 4,
            ],
            [
                'slug' => 'mentoring-at-ut-dallas',
                'category' => 'Team Collaboration',
                'title' => 'Mentoring aspiring developers through UT Dallas',
                'summary' => 'Miguel mentors students through a coding mentorship program at UT Dallas.',
                'content' => <<<'TEXT'
                Miguel has mentored aspiring developers through a coding mentorship program at UT Dallas (The University of Texas at Dallas). Mentorship is one of the things he wants to keep doing in his next role, alongside hands-on engineering and technical leadership.
                TEXT,
                'tags' => ['mentorship', 'mentoring', 'teaching', 'ut-dallas', 'students', 'community', 'leadership'],
                'importance' => 3,
            ],

            // --------------------------------------------------------- Career
            [
                'slug' => 'career-timeline',
                'category' => 'Career',
                'title' => 'Career timeline',
                'summary' => 'Overview of Miguel\'s roles from 2008 to today: A.M. Design, Ivie, RealPage, Jonah Digital, plus independent AI-first product work.',
                'content' => <<<'TEXT'
                - 2024 — Present: Senior Software Developer at Jonah Digital — builds and maintains the products behind Jonah's apartment marketing platform for the multifamily industry.
                - 2024 — Present: Independent senior engineer — AI-first products including QCP Staffing / Minute and Appointment Hub.
                - 2015 — 2024: Director of Web Development at RealPage — led a team of developers building B2B and B2C marketing websites, events, and CRM/CMS platforms (including realpage.com, Kigo, and Propertyware).
                - 2013 — 2015: Web Developer at Ivie — digital media, marketing landing pages, and custom content management systems (including the Fresh Thyme CMS application).
                - 2008 — 2013: Lead Web Developer at A.M. Design — websites, mobile sites, and apps for clients including Commscope, Watermark Church, and Red Rocks Church; custom CMS work; technical direction.
                TEXT,
                'tags' => ['career', 'timeline', 'history', 'resume', 'roles', 'employers', 'experience', 'years'],
                'importance' => 5,
            ],
            [
                'slug' => 'jonah-digital-senior-software-developer',
                'category' => 'Career',
                'title' => 'Senior Software Developer at Jonah Digital (2024 — present)',
                'summary' => 'Builds and maintains the products behind Jonah\'s apartment marketing platform for the multifamily industry, working full-stack with an AI-assisted workflow.',
                'content' => <<<'TEXT'
                Since 2024 Miguel has been a Senior Software Developer at Jonah Digital (jonahdigital.com). He builds and maintains the products behind Jonah's apartment marketing platform, which powers property websites and landing pages across the multifamily (apartment) industry.

                He develops across the full stack with an AI-assisted workflow, focusing on architecture, performance, and code quality.
                TEXT,
                'tags' => ['jonah-digital', 'current-role', 'multifamily', 'apartment-marketing', 'full-stack', 'senior-developer', 'real-estate'],
                'importance' => 4,
            ],
            [
                'slug' => 'realpage-director-of-web-development',
                'category' => 'Leadership',
                'title' => 'Director of Web Development at RealPage (2015 — 2024)',
                'summary' => 'Nine years leading a development team at RealPage: marketing websites, CRM and CMS platforms, a shared design library, and tools that cut costs and improved efficiency.',
                'content' => <<<'TEXT'
                From 2015 to 2024 Miguel was Director of Web Development at RealPage, a property management software company. He led and managed a team of developers building B2B and B2C marketing websites, event sites, and CRM and CMS platforms — including realpage.com, Kigo, and Propertyware.

                Highlights from that time:
                - Delivered essential marketing tools that improved efficiency by 70% and reduced costs by 60%.
                - Implemented a design library that standardized components, boosting code maintainability by 40%.
                - Designed an API system and drove cross-team collaboration.
                - Built and evolved the RealPage corporate marketing site (2024) with his team, along with the CMS platform behind it.
                - Designed and shipped the Propertyware marketing site (2019) with his team.
                TEXT,
                'tags' => ['realpage', 'director', 'leadership', 'management', 'team-lead', 'managing-developers', 'cms', 'crm', 'design-system', 'marketing-websites', 'property-management'],
                'importance' => 5,
            ],
            [
                'slug' => 'ivie-web-developer',
                'category' => 'Career',
                'title' => 'Web Developer at Ivie (2013 — 2015)',
                'summary' => 'Digital media, marketing landing pages, and custom CMS work at the agency Ivie, including the Fresh Thyme CMS application.',
                'content' => <<<'TEXT'
                From 2013 to 2015 Miguel was a Web Developer at Ivie, an agency. He built digital media, marketing landing pages, and custom content management systems — including the Fresh Thyme CMS application — and did responsive web and mobile development, API design and integration, and project scope writing.
                TEXT,
                'tags' => ['ivie', 'agency', 'cms', 'fresh-thyme', 'landing-pages', 'responsive', 'api'],
                'importance' => 3,
            ],
            [
                'slug' => 'am-design-lead-web-developer',
                'category' => 'Career',
                'title' => 'Lead Web Developer at A.M. Design (2008 — 2013)',
                'summary' => 'Miguel\'s first lead role: websites, mobile sites, and apps for clients such as Commscope, Watermark Church, and Red Rocks Church.',
                'content' => <<<'TEXT'
                From 2008 to 2013 Miguel was Lead Web Developer at A.M. Design. He developed websites, mobile sites, and apps for clients including Commscope, Watermark Church, and Red Rocks Church, and modified and extended a custom CMS.

                He provided technical direction on technology choices and presented on emerging technologies and best practices — his first experience setting technical direction for others.
                TEXT,
                'tags' => ['am-design', 'agency', 'lead-developer', 'technical-direction', 'cms', 'mobile', 'early-career'],
                'importance' => 3,
            ],
            [
                'slug' => 'brands-miguel-has-built-for',
                'category' => 'Career',
                'title' => 'Brands Miguel has built for',
                'summary' => 'AT&T, Harley-Davidson, Travel Channel, Sonic, Cricket Wireless, RealPage, Twin Peaks, Which Wich, Fresh Thyme, and Stagen — directly or through agency work.',
                'content' => <<<'TEXT'
                Over his career, directly or through agency work, Miguel has built for brands including AT&T, Harley-Davidson, Travel Channel, Sonic, Cricket Wireless, RealPage, Twin Peaks, Which Wich, Fresh Thyme, and Stagen. Agency-era client work also included Commscope, Watermark Church, and Red Rocks Church.
                TEXT,
                'tags' => ['brands', 'clients', 'agency', 'att', 'harley-davidson', 'sonic', 'cricket-wireless', 'enterprise'],
                'importance' => 2,
            ],

            // ------------------------------------------------------- Projects
            [
                'slug' => 'project-qcp-staffing-minute',
                'category' => 'Projects',
                'title' => 'QCP Staffing / Minute',
                'summary' => 'A staffing platform that manages the full staffing workflow end to end — one of two major applications Miguel built AI-first over the last two years.',
                'content' => <<<'TEXT'
                QCP Staffing / Minute is a staffing platform that manages the full staffing workflow end to end. It is one of the two major applications Miguel built with an AI-first workflow over the last two years, developed end to end as an independent senior engineer.

                Stack and delivery: PHP/Laravel, MySQL, Redis, AI-assisted development, CI/CD, hosted on AWS.
                TEXT,
                'tags' => ['qcp-staffing', 'minute', 'staffing', 'saas', 'laravel', 'mysql', 'redis', 'aws', 'ci-cd', 'ai-first', 'proud', 'recent-work', 'platform'],
                'importance' => 5,
            ],
            [
                'slug' => 'project-appointment-hub',
                'category' => 'Projects',
                'title' => 'Appointment Hub',
                'summary' => 'An appointment scheduling platform that streamlines booking for businesses and their clients, developed with an AI-first workflow.',
                'content' => <<<'TEXT'
                Appointment Hub is an appointment scheduling platform that streamlines booking for businesses and their clients. Along with QCP Staffing / Minute, it is one of Miguel's two most recent applications, developed end to end using an AI-enabled approach.

                Stack and delivery: PHP/Laravel, MySQL, AI-assisted development, CI/CD, hosted on DigitalOcean.
                TEXT,
                'tags' => ['appointment-hub', 'scheduling', 'booking', 'saas', 'laravel', 'mysql', 'digitalocean', 'ci-cd', 'ai-first', 'proud', 'recent-work'],
                'importance' => 5,
            ],
            [
                'slug' => 'project-surenut',
                'category' => 'Projects',
                'title' => 'SureNut marketing site and dashboard',
                'summary' => 'Product marketing site for SureNut®, the first reusable prevailing torque wheel fastener, plus the back-end dashboard that runs it.',
                'content' => <<<'TEXT'
                SureNut is a product marketing site for SureNut® — introducing the first reusable prevailing torque wheel fastener and turning visitors into leads. Miguel handled the UI/UX and built it with PHP/Laravel and MySQL.

                The SureNut Dashboard is the back-end dashboard powering surenut.com — the operational side of the product, from lead management to site content. Built with PHP/Laravel and MySQL using AI-assisted development.
                TEXT,
                'tags' => ['surenut', 'marketing-site', 'dashboard', 'lead-management', 'ui-ux', 'laravel', 'mysql', 'cms', 'product-site'],
                'importance' => 3,
            ],
            [
                'slug' => 'project-realpage-marketing-site',
                'category' => 'Projects',
                'title' => 'RealPage corporate marketing site and CMS',
                'summary' => 'The corporate marketing site for RealPage (2024) and the CMS platform behind it, built and evolved with the team Miguel led.',
                'content' => <<<'TEXT'
                The RealPage corporate marketing site (2024) was built and evolved by the team Miguel led as Director of Web Development, along with the CMS platform behind it. He acted as team lead and contributed to UI/UX; the stack was PHP/Laravel and MySQL.
                TEXT,
                'tags' => ['realpage', 'marketing-site', 'cms', 'team-lead', 'ui-ux', 'laravel', 'mysql', 'enterprise'],
                'importance' => 3,
            ],
            [
                'slug' => 'project-propertyware',
                'category' => 'Projects',
                'title' => 'Propertyware marketing site',
                'summary' => 'Marketing site for Propertyware (2019), RealPage\'s single-family property management platform, designed and shipped with Miguel\'s team.',
                'content' => <<<'TEXT'
                Propertyware is RealPage's single-family property management platform. In 2019 Miguel and his team designed and shipped its marketing site. He was team lead and contributed to UI/UX; the stack was PHP/Laravel and MySQL.
                TEXT,
                'tags' => ['propertyware', 'realpage', 'marketing-site', 'team-lead', 'ui-ux', 'laravel', 'mysql', 'property-management'],
                'importance' => 3,
            ],

            // ----------------------------------------------- Technical skills
            [
                'slug' => 'laravel-and-php',
                'category' => 'Technical Skills',
                'title' => 'Laravel and PHP',
                'summary' => 'Laravel is Miguel\'s primary back-end framework: it powers QCP Staffing / Minute, Appointment Hub, SureNut, the RealPage and Propertyware marketing sites, and his own portfolio.',
                'content' => <<<'TEXT'
                PHP and Laravel are the backbone of Miguel's back-end work. Laravel powers his most recent products — QCP Staffing / Minute and Appointment Hub — as well as the SureNut site and dashboard, the RealPage corporate marketing site and its CMS, and the Propertyware marketing site.

                His own portfolio site is a Laravel application rendered with Blade, with a Pest test suite, and deployed with Laravel Forge in his toolbox. He pairs Laravel with MySQL on every one of those projects, and with Redis on QCP Staffing / Minute.
                TEXT,
                'tags' => ['laravel', 'php', 'backend', 'framework', 'blade', 'pest', 'eloquent', 'mysql', 'api'],
                'importance' => 5,
            ],
            [
                'slug' => 'languages-and-frameworks',
                'category' => 'Technical Skills',
                'title' => 'Languages and frameworks',
                'summary' => 'HTML, CSS, JavaScript, TypeScript, React, Vue, PHP, Laravel, and Tailwind CSS.',
                'content' => <<<'TEXT'
                Miguel's core languages and frameworks are HTML, CSS, JavaScript, TypeScript, React JS, Vue JS, PHP, Laravel, and Tailwind CSS. He works across the full stack: front-end interfaces (with a strong eye for design and accessibility) and back-end application architecture.
                TEXT,
                'tags' => ['skills', 'technologies', 'languages', 'frameworks', 'javascript', 'typescript', 'react', 'vue', 'tailwind', 'css', 'html', 'frontend', 'full-stack'],
                'importance' => 5,
            ],
            [
                'slug' => 'infrastructure-and-delivery',
                'category' => 'Technical Skills',
                'title' => 'Infrastructure, databases, and delivery',
                'summary' => 'MySQL, Redis, AWS, DigitalOcean, CI/CD, Git, Docker, Laravel Forge, and automated testing with Pest/PHPUnit.',
                'content' => <<<'TEXT'
                On the infrastructure and delivery side, Miguel works with MySQL (his primary database across projects), Redis, AWS (QCP Staffing / Minute), DigitalOcean (Appointment Hub), CI/CD pipelines, Git, Docker, Laravel Forge, and automated testing with Pest and PHPUnit.
                TEXT,
                'tags' => ['infrastructure', 'devops', 'database', 'mysql', 'redis', 'aws', 'digitalocean', 'ci-cd', 'git', 'docker', 'forge', 'testing', 'pest', 'phpunit', 'deployment'],
                'importance' => 4,
            ],
            [
                'slug' => 'ai-first-development-workflow',
                'category' => 'Technical Skills',
                'title' => 'AI-first development workflow',
                'summary' => 'Two-plus years of daily AI-assisted development with Claude Code, the Anthropic Claude API, and the OpenAI API; two production applications built end to end this way.',
                'content' => <<<'TEXT'
                Over the past two years, AI-assisted development has become an important part of Miguel's day-to-day workflow. He uses tools such as Claude Code daily and works directly with the Anthropic Claude API and the OpenAI API to accelerate research, prototyping, implementation, and refinement.

                His two most recent applications, QCP Staffing / Minute and Appointment Hub, were developed end to end using this AI-enabled approach. He also ships AI-powered product features, such as the AI assistant on his own portfolio, which answers visitor questions from a curated knowledge base.
                TEXT,
                'tags' => ['ai', 'claude-code', 'claude-api', 'anthropic', 'openai', 'llm', 'ai-first', 'ai-assisted', 'workflow', 'productivity', 'ai-products'],
                'importance' => 5,
            ],

            // ------------------------------------------------ Accomplishments
            [
                'slug' => 'realpage-efficiency-and-cost-results',
                'category' => 'Accomplishments',
                'title' => 'Measurable results at RealPage',
                'summary' => 'Marketing tools that improved efficiency 70% and cut costs 60%; a design library that improved maintainability 40%; an API system that enabled cross-team collaboration.',
                'content' => <<<'TEXT'
                As Director of Web Development at RealPage, Miguel delivered essential marketing tools that improved efficiency by 70% and reduced costs by 60%. He implemented a design library that standardized components across properties, boosting code maintainability by 40%, and designed an API system while driving collaboration across teams.
                TEXT,
                'tags' => ['accomplishments', 'results', 'metrics', 'impact', 'efficiency', 'cost-savings', 'design-system', 'api', 'realpage'],
                'importance' => 4,
            ],

            // ------------------------------------------ STAR story template
            [
                'slug' => 'example-star-story-template',
                'category' => 'STAR Stories',
                'kind' => AiKnowledgeEntry::KIND_STAR,
                'title' => 'Example STAR story (template — replace me)',
                'summary' => 'A placeholder showing the STAR format. Inactive, so the assistant never sees it. Replace it with a real story or delete it.',
                'situation' => 'Set the scene: the company, the project, the constraint, what was at stake. Example: "A product launch date was fixed by a trade show and the marketing site was three weeks behind."',
                'task' => 'What you were responsible for. Example: "I owned delivery of the site and the CMS behind it with a team of four."',
                'action' => 'What you personally did, step by step. Example: "I cut scope with the stakeholders, split the work into daily releases, paired with the two newest developers, and moved the CMS to a component library so pages could be assembled instead of coded."',
                'result' => 'The outcome, with numbers where you have them. Example: "We shipped two days early; the same team produced pages 70% faster afterwards."',
                'tags' => ['deadline', 'leadership', 'project-management', 'prioritization', 'communication', 'pressure'],
                'importance' => 3,
                'is_active' => false,
            ],
        ];
    }
}
