<header data-reveal class="relative overflow-hidden px-[7vw] pb-[130px] pt-[120px] text-center">
    <div class="pointer-events-none absolute -top-[160px] left-1/2 h-[720px] w-[720px] -translate-x-1/2 bg-[radial-gradient(circle,rgba(46,230,166,.18),transparent_62%)]"></div>
    <div class="pointer-events-none absolute inset-0 bg-[linear-gradient(rgb(255_255_255/0.028)_1px,transparent_1px),linear-gradient(90deg,rgb(255_255_255/0.028)_1px,transparent_1px)] bg-[size:64px_64px] [mask-image:radial-gradient(circle_at_50%_30%,#000,transparent_72%)]"></div>
    <div class="relative mx-auto max-w-[940px]">
        <span class="mb-[30px] inline-flex items-center gap-[9px] rounded-full border border-accent/30 px-[15px] py-[7px] text-[13px] font-semibold text-accent">
            <span class="h-[7px] w-[7px] rounded-full bg-accent shadow-[0_0_10px_#2ee6a6]"></span>
            {{ config('portfolio.availability') }}
        </span>
        <h1 class="mb-[26px] font-display text-[clamp(42px,6.6vw,86px)] font-bold leading-[1.02] tracking-[-0.035em]">
            Building beautiful<br>
            web &amp; mobile <span class="text-accent">experiences</span>
        </h1>
        <p class="mx-auto mb-10 max-w-[600px] text-[19px] leading-[1.6] text-muted">
            I'm {{ config('portfolio.name') }} — a senior full-stack engineer who's spent
            15+ years turning ideas into fast, polished products. I've led teams, and for
            the past two years I've built AI-first every day.
        </p>
        <x-portfolio.hero.actions center />
    </div>
</header>
