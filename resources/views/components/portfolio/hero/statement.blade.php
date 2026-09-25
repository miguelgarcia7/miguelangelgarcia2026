<header data-reveal class="relative overflow-hidden px-[7vw] pb-[130px] pt-[120px] text-center">
    <div class="pointer-events-none absolute -top-[160px] left-1/2 h-[720px] w-[720px] -translate-x-1/2 bg-[radial-gradient(circle,rgba(46,230,166,.18),transparent_62%)]"></div>
    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgb(255_255_255/0.028)_1px,transparent_1px),linear-gradient(90deg,rgb(255_255_255/0.028)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(circle_at_50%_30%,#000,transparent_72%)]"></div>
    <div class="relative mx-auto max-w-[940px]">
        <span class="mb-[30px] inline-flex items-center gap-[9px] rounded-full border border-accent/30 px-[15px] py-[7px] text-[13px] font-semibold text-accent">
            <span class="h-[7px] w-[7px] rounded-full bg-accent shadow-[0_0_10px_#2ee6a6]"></span>
            {{ config('portfolio.availability') }}
        </span>
        <h1 class="mb-[26px] font-display text-[clamp(42px,6.6vw,86px)] font-bold leading-[1.02] tracking-[-0.035em]">
            Thoughtful software,<br>
            <span class="text-accent">engineered</span> <span class="whitespace-nowrap">end to end.</span>
        </h1>
        <p class="mx-auto mb-10 max-w-[640px] text-[19px] leading-[1.6] text-muted">
            I'm {{ config('portfolio.name') }}, a {{ config('portfolio.job_title') }} specializing in
            <span class="whitespace-nowrap">AI-driven</span> development and LLM integrations. I architect, build, and ship robust,
            highly maintainable products—collaborating closely with designers to perfect the user
            experience, and empowering the teams that make them happen.
        </p>
        @if (config('ai.enabled'))
            <p class="mx-auto mb-5 max-w-[640px] text-[17px] font-semibold leading-[1.5] text-ink">
                Have a question? <span class="font-normal text-muted">Ask my AI assistant below anything about my work.</span>
            </p>
            <x-portfolio.ask />
        @else
            <x-portfolio.hero.actions center />
        @endif
    </div>
</header>
