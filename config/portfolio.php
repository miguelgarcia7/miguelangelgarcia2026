<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    |
    | Identity facts used across the site and in the JSON-LD structured data.
    | Add your public profiles to "same_as" (GitHub, LinkedIn, X, …) — these
    | links are how search engines and AI crawlers connect this site to the
    | rest of your presence on the web.
    |
    */

    'name' => 'Miguel Angel Garcia',
    'short_name' => 'Miguel Garcia',
    'job_title' => 'Senior Full-Stack Engineer',
    'availability' => 'Available for new opportunities',
    'location' => 'Dallas–Fort Worth, TX',

    // Where contact form submissions are delivered.
    'contact_email' => env('CONTACT_TO_ADDRESS', env('MAIL_FROM_ADDRESS')),
    'description' => 'Miguel Angel Garcia is a senior full-stack engineer in Dallas–Fort Worth with 15+ years building websites and web applications, leading development teams, and shipping AI-powered products with Laravel, React, Vue, and the Claude and OpenAI APIs.',
    'same_as' => [
        'https://miguelangelgarcia.com',
        // 'https://github.com/your-username',
        // 'https://www.linkedin.com/in/your-username',
    ],

    /*
    |--------------------------------------------------------------------------
    | Projects
    |--------------------------------------------------------------------------
    |
    | All six are real projects. TODO(Miguel): correct descriptions and
    | tags where needed — some are drafts written from limited context.
    |
    */

    'projects' => [
        [
            'title' => 'QCP Staffing / Minute',
            'description' => 'Staffing platform managing the full workflow end-to-end. One of two major applications built AI-first over the last two years.',
            'tags' => ['Laravel', 'MySQL', 'AI-assisted', 'CI/CD'],
            'image' => '/images/projects/qcpstaffing.png',
        ],
        [
            'title' => 'Appointment Hub',
            'description' => 'Appointment scheduling platform that streamlines booking for businesses and their clients, developed with an AI-first workflow.',
            'tags' => ['Laravel', 'MySQL', 'AI-assisted', 'CI/CD'],
            'image' => '/images/projects/appointmenthub.png',
        ],
        [
            'title' => 'SureNut',
            'description' => 'Product marketing site for SureNut® — introducing the first reusable prevailing torque wheel fastener and turning visitors into leads.',
            'tags' => ['UI/UX', 'Front-end', 'Marketing Site'],
            'image' => '/images/projects/surenut.png',
        ],
        [
            'title' => 'SureNut Dashboard',
            'description' => 'Back-end dashboard powering surenut.com — the operational side of the product, from lead management to site content.',
            'tags' => ['Laravel', 'MySQL', 'Back-end'],
            'image' => '/images/projects/surenut-dashboard.png',
        ],
        [
            'title' => 'RealPage',
            'description' => 'The corporate marketing site for RealPage (2024)  — built and evolved with my team while leading web development, along with the CMS platform behind it.',
            'tags' => ['UI/UX', 'CMS', 'Team Lead'],
            'image' => '/images/projects/realpage.png',
        ],
        [
            'title' => 'Propertyware',
            'description' => 'Marketing site for Propertyware (2019), RealPage\'s single-family property management platform — designed and shipped with my team.',
            'tags' => ['UI/UX', 'CMS', 'Team Lead'],
            'image' => '/images/projects/propertyware.png',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Experience
    |--------------------------------------------------------------------------
    */

    'experience' => [
        [
            'period' => '2024 — Present',
            'role' => 'Senior Software Developer',
            'org' => 'Jonah Digital',
            'points' => [
                'Build and maintain the products behind Jonah\'s apartment marketing platform — powering property sites and landing pages across the multifamily industry.',
                'Develop across the full stack with an AI-assisted workflow, focusing on architecture, performance, and code quality.',
            ],
        ],
        [
            'period' => '2015 — 2024',
            'role' => 'Director of Web Development',
            'org' => 'RealPage',
            'points' => [
                'Led and managed a team of developers building B2B & B2C marketing websites, events, CRM and CMS platforms — including realpage.com, Kigo, and Propertyware.',
                'Delivered essential marketing tools that improved efficiency by 70% and reduced costs by 60%.',
                'Implemented a design library standardizing components, boosting code maintainability by 40%.',
                'Designed an API system and drove cross-team collaboration.',
            ],
        ],
        [
            'period' => '2013 — 2015',
            'role' => 'Web Developer',
            'org' => 'Ivie',
            'points' => [
                'Built digital media, marketing landing pages, and custom content management systems — including the Fresh Thyme CMS application.',
                'Responsive web & mobile development, API design and integration, and project scope writing.',
            ],
        ],
        [
            'period' => '2008 — 2013',
            'role' => 'Lead Web Developer',
            'org' => 'A.M. Design',
            'points' => [
                'Developed websites, mobile sites, and apps for clients including Commscope, Watermark Church, and Red Rocks Church; modified and extended a custom CMS.',
                'Provided technical direction on technology choices and presented on emerging technologies and best practices.',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Brands
    |--------------------------------------------------------------------------
    |
    | Companies and brands Miguel has built for (directly or through
    | agencies). Rendered as subtle text wordmarks; to use a real logo,
    | drop an SVG in public/images/brands/ and set its path as "logo".
    |
    */

    'brands' => [
        ['name' => 'AT&T', 'logo' => null],
        ['name' => 'Harley-Davidson', 'logo' => null],
        ['name' => 'Travel Channel', 'logo' => null],
        ['name' => 'Sonic', 'logo' => null],
        ['name' => 'Cricket Wireless', 'logo' => null],
        ['name' => 'RealPage', 'logo' => null],
        ['name' => 'Twin Peaks', 'logo' => null],
        ['name' => 'Which Wich', 'logo' => null],
        ['name' => 'Fresh Thyme', 'logo' => null],
        ['name' => 'Stagen', 'logo' => null],
    ],

    /*
    |--------------------------------------------------------------------------
    | Toolbox
    |--------------------------------------------------------------------------
    */

    'stack_groups' => [
        [
            'label' => 'Languages & Frameworks',
            'items' => [
                ['name' => 'HTML', 'glyph' => '<>', 'background' => 'rgba(227,79,38,.14)', 'color' => '#f0754e'],
                ['name' => 'CSS', 'glyph' => '{ }', 'background' => 'rgba(38,77,228,.16)', 'color' => '#7c96ff'],
                ['name' => 'JavaScript', 'glyph' => 'JS', 'background' => 'rgba(240,219,79,.14)', 'color' => '#f0db4f'],
                ['name' => 'React JS', 'glyph' => '⚛', 'background' => 'rgba(97,218,251,.14)', 'color' => '#61dafb'],
                ['name' => 'Vue JS', 'glyph' => 'V', 'background' => 'rgba(65,184,131,.16)', 'color' => '#42d392'],
                ['name' => 'TypeScript', 'glyph' => 'TS', 'background' => 'rgba(49,120,198,.18)', 'color' => '#7cb2f0'],
                ['name' => 'PHP', 'glyph' => 'php', 'background' => 'rgba(119,123,180,.18)', 'color' => '#a8adde'],
                ['name' => 'Laravel', 'glyph' => 'L', 'background' => 'rgba(255,45,45,.14)', 'color' => '#ff6b6b'],
                ['name' => 'Tailwind CSS', 'glyph' => '≈', 'background' => 'rgba(56,189,248,.14)', 'color' => '#4cc8f8'],
            ],
        ],
        [
            'label' => 'Infrastructure & Delivery',
            'items' => [
                ['name' => 'MySQL', 'glyph' => 'SQL', 'background' => 'rgba(0,117,143,.2)', 'color' => '#5cc6e0'],
                ['name' => 'AWS', 'glyph' => 'aws', 'background' => 'rgba(255,153,0,.14)', 'color' => '#ff9f30'],
                ['name' => 'CI/CD', 'glyph' => 'CI', 'background' => 'rgba(52,211,153,.14)', 'color' => '#34d399'],
                ['name' => 'Git', 'glyph' => 'git', 'background' => 'rgba(240,80,50,.14)', 'color' => '#f05032'],
                ['name' => 'Docker', 'glyph' => 'D', 'background' => 'rgba(29,99,237,.16)', 'color' => '#5a8cf5'],
                ['name' => 'Laravel Forge', 'glyph' => 'F', 'background' => 'rgba(24,182,155,.14)', 'color' => '#2fd0b5'],
                ['name' => 'Automated Testing', 'glyph' => '✓', 'background' => 'rgba(168,133,255,.14)', 'color' => '#b79df5'],
            ],
        ],
        [
            'label' => 'AI & Workflow',
            'items' => [
                ['name' => 'Claude Code', 'glyph' => 'CC', 'background' => 'rgba(217,119,87,.16)', 'color' => '#d97757'],
                ['name' => 'Claude API', 'glyph' => '✳', 'background' => 'rgba(217,119,87,.12)', 'color' => '#e08b6d'],
                ['name' => 'OpenAI API', 'glyph' => '◎', 'background' => 'rgba(16,163,127,.16)', 'color' => '#3ccf9e'],
            ],
        ],
    ],

];
