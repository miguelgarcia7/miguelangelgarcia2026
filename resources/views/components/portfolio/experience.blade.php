<section id="experience" data-reveal class="pf-section">
    <x-portfolio.section-intro
        eyebrow="Career"
        title="Experience"
        lead="15+ years across agencies, product companies, and independent work — as an engineer, a lead, and a director."
    />
    <div class="pf-xp">
        @foreach (config('portfolio.experience') as $job)
            <div class="pf-xp__item">
                <span class="pf-xp__dot" aria-hidden="true"></span>
                <p class="pf-xp__period">{{ $job['period'] }}</p>
                <h3 class="pf-xp__role">{{ $job['role'] }}</h3>
                <p class="pf-xp__org">{{ $job['org'] }}</p>
                <ul class="pf-xp__points">
                    @foreach ($job['points'] as $point)
                        <li>{{ $point }}</li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</section>
