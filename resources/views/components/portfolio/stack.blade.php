<section id="stack" data-reveal class="pf-section">
    <x-portfolio.section-intro
        eyebrow="Toolbox"
        title="Skills & Tools"
        lead="The languages, infrastructure, and AI tooling I reach for to design, build and ship full-stack products."
    >
        <x-portfolio.quote author="Steve Jobs">To turn really interesting ideas and fledgling technologies into a company that can continue to innovate for years, it requires a lot of disciplines.</x-portfolio.quote>
    </x-portfolio.section-intro>
    @foreach (config('portfolio.stack_groups') as $group)
        <div class="pf-stack__group">
            <h3 class="pf-stack__group-label">{{ $group['label'] }}</h3>
            <div class="pf-stack__grid">
                @foreach ($group['items'] as $item)
                    <div class="pf-stack-card">
                        <span
                            class="pf-stack-card__line"
                            style="background: linear-gradient(90deg, transparent, {{ $item['color'] }}, transparent)"
                        ></span>
                        <span
                            class="pf-stack-card__glyph"
                            style="background: {{ $item['background'] }}; color: {{ $item['color'] }}"
                        >{{ $item['glyph'] }}</span>
                        <span class="pf-stack-card__name">{{ $item['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</section>
