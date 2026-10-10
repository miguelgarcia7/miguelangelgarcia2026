<section id="experience" data-reveal tabindex="0" aria-labelledby="experience-heading" class="pf-divider isolate px-[7vw] py-24">
    <div class="pf-glow pf-glow--experience" aria-hidden="true"></div>
    <x-portfolio.section-intro
        eyebrow="Career"
        title="Experience"
        heading-id="experience-heading"
        lead="15+ years across agencies, product companies, and independent work — as an engineer, a lead, and a director."
    />
    {{-- pf-timeline draws the line: green at the current role, fading out
         toward the oldest. The dots follow it from green to teal to grey. --}}
    <div class="pf-timeline mx-auto max-w-[760px]">
        @foreach (config('portfolio.experience') as $job)
            <div @class([
                'relative border-l border-transparent pl-[34px]',
                'pb-[46px]' => ! $loop->last,
            ])>
                <span @class([
                    'absolute -left-[4.5px] top-[5px] h-2 w-2 rounded-full',
                    'bg-accent shadow-[0_0_10px_#2ee6a6]' => $loop->first,
                    'bg-teal shadow-[0_0_10px_#22c3d6]' => $loop->index === 1,
                    'bg-[#5d8a95]' => $loop->index > 1,
                ]) aria-hidden="true"></span>
                <p @class([
                    'mb-2 text-[13px] font-semibold uppercase tracking-[0.1em]',
                    'pf-gradient-text' => $loop->first,
                    'text-accent' => ! $loop->first,
                ])>{{ $job['period'] }}</p>
                <h3 class="mb-1 font-display text-[21px] font-semibold tracking-[-0.02em]">{{ $job['role'] }}</h3>
                <p class="mb-[14px] text-[14.5px] font-semibold text-soft">{{ $job['org'] }}</p>
                <ul class="list-none space-y-[7px] p-0 text-[15.5px] leading-[1.7] text-body">
                    @foreach ($job['points'] as $point)
                        <li class="relative pl-[22px] before:absolute before:left-0 before:top-[11px] before:h-0.5 before:w-[11px] before:bg-accent before:opacity-55 before:content-['']">
                            {{ $point }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    </div>
</section>
