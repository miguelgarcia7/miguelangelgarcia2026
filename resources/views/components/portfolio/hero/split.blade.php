<header data-reveal class="relative grid items-center gap-14 overflow-hidden px-[7vw] pb-[120px] pt-[110px] max-[960px]:grid-cols-1 min-[961px]:grid-cols-[1.1fr_0.9fr]">
    <div class="pointer-events-none absolute -right-20 -top-[140px] h-[640px] w-[640px] bg-[radial-gradient(circle,rgba(46,230,166,.16),transparent_62%)]"></div>
    <div class="relative">
        <span class="mb-[26px] inline-flex items-center gap-[9px] rounded-full border border-accent/30 px-[15px] py-[7px] text-[13px] font-semibold text-accent">
            <span class="h-[7px] w-[7px] rounded-full bg-accent shadow-[0_0_10px_#2ee6a6]"></span>
            {{ config('portfolio.job_title') }}
        </span>
        <h1 class="mb-6 font-display text-[clamp(40px,5.4vw,74px)] font-bold leading-[1.04] tracking-[-0.035em]">
            Building beautiful<br>
            web &amp; mobile<br>
            <span class="text-accent">experiences</span>
        </h1>
        <p class="mb-[38px] max-w-[480px] text-[18.5px] leading-[1.6] text-muted">
            Hi, I'm {{ config('portfolio.name') }}. For 15+ years I've engineered fast,
            interactive products, led development teams, and now build AI-powered
            applications — with a designer's eye for detail.
        </p>
        <x-portfolio.hero.actions />
    </div>
    <div class="relative max-[960px]:max-w-[420px]">
        <div class="absolute -inset-0.5 rounded-[30px] bg-[linear-gradient(150deg,rgba(46,230,166,.5),transparent_55%)] blur-[2px]"></div>
        <div class="relative aspect-4/5 overflow-hidden rounded-[28px] border border-white/8">
            <x-portfolio.image-slot label="Drop your photo" :alt="config('portfolio.name')" />
        </div>
    </div>
</header>
