<section id="about" data-reveal class="grid items-center gap-16 border-t border-line px-[7vw] py-24 max-[960px]:grid-cols-1 max-[960px]:gap-11 min-[961px]:grid-cols-[0.85fr_1.15fr]">
    <div class="relative max-[960px]:max-w-[420px]">
        <div class="pointer-events-none absolute -inset-[14px] rounded-[32px] bg-[radial-gradient(circle_at_30%_20%,rgba(46,230,166,.22),transparent_60%)]"></div>
        <div class="relative aspect-square overflow-hidden rounded-[26px] border border-white/9">
            <x-portfolio.image-slot label="Drop your photo" :alt="config('portfolio.name')" />
        </div>
    </div>
    <div>
        <p class="mb-4 text-sm font-semibold uppercase tracking-[0.14em] text-accent">About me</p>
        <h2 class="mb-[15px] font-display text-[clamp(30px,3.6vw,44px)] font-bold leading-[1.1] tracking-[-0.03em]">
            The web has changed a lot in 15 years. So have I.
        </h2>
        <x-portfolio.quote author="Steve Jobs" class="mb-[26px]">Stay hungry, stay foolish.</x-portfolio.quote>
        <p class="mb-[18px] text-[17px] leading-[1.72] text-body">
            I'm a full-stack engineer with more than 15 years of experience building thoughtful,
            reliable digital products for web and mobile users. My career has evolved from
            designing and developing websites to architecting complex web applications, leading
            technical initiatives, and collaborating with engineers, designers, and business
            stakeholders to turn ideas into scalable solutions.
        </p>
        <p class="mb-[18px] text-[17px] leading-[1.72] text-body">
            I bring a strong appreciation for interface design and user experience, along with a
            deep focus on architecture, maintainability, accessibility, and delivery. I care not
            only about what a team builds, but also how it collaborates, makes decisions, and
            consistently ships high-quality work. I have also mentored aspiring developers through
            a coding mentorship program at UT Dallas.
        </p>
        <p class="mb-[18px] text-[17px] leading-[1.72] text-body">
            I'm interested in senior engineering opportunities where I can combine hands-on
            development with technical leadership, architecture, mentorship, and product thinking.
            That may take the form of an engineering leadership position or a senior individual
            contributor role, depending on the team and the problems being solved.
        </p>
        <p class="mb-7 text-[17px] leading-[1.72] text-body">
            Over the past two years, AI-assisted development has become an important part of my
            day-to-day workflow. I use tools such as Claude Code and work directly with the
            Anthropic and OpenAI APIs to accelerate research, prototyping, implementation, and
            refinement. My two most recent applications, QCP Staffing / Minute and Appointment
            Hub, were developed end to end using this AI-enabled approach.
        </p>
        <p class="font-display text-[19px] font-semibold text-ink">
            Never. Stop. <span class="text-accent">Learning.</span>
        </p>
        <div class="mt-9 flex flex-wrap gap-11">
            <div>
                <div class="font-display text-[34px] font-bold text-accent">15+</div>
                <div class="mt-0.5 text-sm text-soft">Years experience</div>
            </div>
            <div>
                <div class="font-display text-[34px] font-bold text-accent">2+</div>
                <div class="mt-0.5 text-sm text-soft">Years of AI-first development</div>
            </div>
            <div>
                <div class="font-display text-[34px] font-bold text-accent">∞</div>
                <div class="mt-0.5 text-sm text-soft">Always learning</div>
            </div>
        </div>
    </div>
</section>
