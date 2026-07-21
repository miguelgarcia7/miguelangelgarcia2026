<header data-reveal class="pf-hero pf-hero--statement">
    <div class="pf-hero__glow"></div>
    <div class="pf-hero__gridlines"></div>
    <div class="pf-hero__content">
        <span class="pf-badge">
            <span class="pf-badge__dot"></span>
            {{ config('portfolio.availability') }}
        </span>
        <h1 class="pf-hero__title">
            Building beautiful<br>
            web &amp; mobile <span class="pf-hero__title-accent">experiences</span>
        </h1>
        <p class="pf-hero__copy">
            I'm {{ config('portfolio.name') }} — a senior full-stack engineer who's spent
            15+ years turning ideas into fast, polished products. I've led teams, and for
            the past two years I've built AI-first every day.
        </p>
        <x-portfolio.hero.actions />
    </div>
</header>
