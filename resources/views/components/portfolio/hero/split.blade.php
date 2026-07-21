<header data-reveal class="pf-hero pf-hero--split">
    <div class="pf-hero__glow"></div>
    <div class="pf-hero__content">
        <span class="pf-badge">
            <span class="pf-badge__dot"></span>
            {{ config('portfolio.job_title') }}
        </span>
        <h1 class="pf-hero__title">
            Building beautiful<br>
            web &amp; mobile<br>
            <span class="pf-hero__title-accent">experiences</span>
        </h1>
        <p class="pf-hero__copy">
            Hi, I'm {{ config('portfolio.name') }}. For 15+ years I've engineered fast,
            interactive products, led development teams, and now build AI-powered
            applications — with a designer's eye for detail.
        </p>
        <x-portfolio.hero.actions />
    </div>
    <div class="pf-hero__photo">
        <div class="pf-hero__photo-glow"></div>
        <div class="pf-hero__photo-frame">
            <x-portfolio.image-slot label="Drop your photo" :alt="config('portfolio.name')" />
        </div>
    </div>
</header>
