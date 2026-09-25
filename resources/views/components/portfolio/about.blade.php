<section id="about" data-reveal tabindex="0" aria-labelledby="about-heading" class="grid items-center gap-16 border-t border-line px-[7vw] py-24 max-[960px]:grid-cols-1 max-[960px]:gap-11 min-[961px]:grid-cols-[0.85fr_1.15fr]">
    <div class="relative max-[960px]:max-w-[420px]">
        <div class="pointer-events-none absolute -inset-[14px] rounded-[32px] bg-[radial-gradient(circle_at_30%_20%,rgba(46,230,166,.22),transparent_60%)]"></div>
        <div class="relative aspect-square overflow-hidden rounded-[26px] border border-white/9">
            <x-portfolio.image-slot label="Drop your photo" :alt="config('portfolio.name')" />
        </div>
    </div>
    <div>
        <p class="mb-4 text-sm font-semibold uppercase tracking-[0.14em] text-accent">About me</p>
        <h2 id="about-heading" class="mb-[15px] font-display text-[clamp(30px,3.6vw,44px)] font-bold leading-[1.1] tracking-[-0.03em]">
            I care how it's built, and how it feels.
        </h2>
        <x-portfolio.quote author="Steve Jobs" class="mb-[26px]">Stay hungry, stay foolish.</x-portfolio.quote>
        <p class="mb-[18px] text-[17px] leading-[1.72] text-body">
            My career has evolved from building websites to architecting complex web applications
            and leading the teams behind them. I partner closely with designers, engineers, and
            stakeholders to transform abstract ideas into scalable, production-ready products.
        </p>

        <h3 class="mb-3 mt-9 font-display text-[20px] font-bold tracking-[-0.02em] text-ink">Where I focus</h3>
        <ul class="flex flex-col gap-3 text-[17px] leading-[1.65] text-body">
            @foreach ([
                'Technical Depth' => 'Architecture, maintainability, accessibility (a11y), and steady, predictable delivery.',
                'Product & Design' => 'Keeping a sharp eye on interface design and intuitive user experience.',
                'Team & Culture' => 'Cultivating healthy collaboration, efficient decision-making, and high-quality shipping standards.',
                'Technical Leadership' => 'Empowering engineering teams through clear architectural guardrails, peer code reviews, and knowledge sharing.',
            ] as $label => $detail)
                <li class="relative pl-6 before:absolute before:left-0 before:top-[0.72em] before:h-[7px] before:w-[7px] before:rounded-full before:bg-accent">
                    <span class="font-semibold text-ink">{{ $label }}:</span> {{ $detail }}
                </li>
            @endforeach
        </ul>

        <h3 class="mb-3 mt-9 font-display text-[20px] font-bold tracking-[-0.02em] text-ink">Modern workflows &amp; AI integration</h3>
        <p class="mb-[18px] text-[17px] leading-[1.72] text-body">
            AI is a core part of my daily engineering toolkit. I leverage tools like Claude Code and
            build directly on the Anthropic and OpenAI APIs to rapidly move through research,
            prototyping, and implementation.
        </p>
        <p class="mb-[18px] text-[17px] leading-[1.72] text-body">
            Crucially, I hold AI-assisted code to the exact same rigorous review, security, and testing
            standards as any other code. My two most recent applications, QCP Staffing / Minute and
            Appointment Hub, are live proof of this high-velocity, high-quality approach.
        </p>

        <h3 class="mb-3 mt-9 font-display text-[20px] font-bold tracking-[-0.02em] text-ink">What's next</h3>
        <p class="text-[17px] leading-[1.72] text-body">
            I am seeking a Senior/Lead Full-Stack Engineering or Tech Lead role that bridges hands-on
            system architecture, technical leadership, and product strategy.
        </p>
        <p class="mt-9 font-display text-[clamp(30px,3.4vw,42px)] font-bold leading-[1.1] tracking-[-0.03em] text-ink">
            Never Stop <span class="text-accent">Learning</span>
        </p>
    </div>
</section>
