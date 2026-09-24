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
            My career has grown from building websites to architecting complex web applications
            and leading the teams behind them. Along the way I've partnered closely with designers,
            engineers, and business stakeholders to turn ideas into products that scale.
        </p>
        <p class="mb-[18px] text-[17px] leading-[1.72] text-body">
            I focus on architecture, maintainability, accessibility, and steady delivery, with a
            sharp eye for good interface design and user experience. I care just as much about how
            a team works: how it collaborates, makes decisions, and ships quality work. I also
            mentor aspiring developers through a coding mentorship program at UT Dallas.
        </p>
        <p class="mb-[18px] text-[17px] leading-[1.72] text-body">
            AI is part of how I work every day. I use Claude Code and build directly on the
            Anthropic and OpenAI APIs to move faster through research, prototyping, and
            implementation, holding that code to the same review and testing standards as any
            other. My two most recent applications, QCP Staffing / Minute and Appointment Hub,
            were built this way.
        </p>
        <p class="mb-7 text-[17px] leading-[1.72] text-body">
            I'm looking for a senior role that combines hands-on engineering with technical
            leadership, architecture, and mentorship, whether as an engineering lead or a senior
            individual contributor.
        </p>
        <p class="mt-9 font-display text-[clamp(30px,3.4vw,42px)] font-bold leading-[1.1] tracking-[-0.03em] text-ink">
            Never Stop <span class="text-accent">Learning</span>
        </p>
    </div>
</section>
