<section id="stack" data-reveal tabindex="-1" aria-labelledby="stack-heading" class="border-t border-line px-[7vw] py-24">
    <x-portfolio.section-intro
        eyebrow="Toolbox"
        title="Skills & Tools"
        heading-id="stack-heading"
        lead="The languages, infrastructure, and AI tooling I reach for to design, build and ship full-stack products."
    >
        <x-portfolio.quote author="Steve Jobs">To turn really interesting ideas and fledgling technologies into a company that can continue to innovate for years, it requires a lot of disciplines.</x-portfolio.quote>
    </x-portfolio.section-intro>
    @foreach (config('portfolio.stack_groups') as $group)
        <div @class(['mx-auto max-w-[1040px]', 'mt-11' => ! $loop->first])>
            <h3 class="mb-[18px] text-center text-[13px] font-semibold uppercase tracking-[0.12em] text-faint">{{ $group['label'] }}</h3>
            <div class="grid gap-[18px] max-[560px]:grid-cols-2 min-[561px]:grid-cols-3 min-[1001px]:grid-cols-5">
                @foreach ($group['items'] as $item)
                    <div class="relative flex flex-col items-center gap-4 overflow-hidden rounded-[18px] border border-line bg-surface-deep px-[18px] py-[30px] text-center">
                        <span
                            class="absolute inset-x-4 top-0 h-0.5 opacity-90"
                            style="background: linear-gradient(90deg, transparent, {{ $item['color'] }}, transparent)"
                        ></span>
                        <span
                            class="grid h-14 w-14 flex-none place-items-center rounded-[15px] font-display text-xl font-bold"
                            style="background: {{ $item['background'] }}; color: {{ $item['color'] }}"
                        >{{ $item['glyph'] }}</span>
                        <span class="text-[15.5px] font-semibold text-[#e7eaee]">{{ $item['name'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</section>
