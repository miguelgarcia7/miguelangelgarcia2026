<section id="brands" data-reveal class="border-t border-line px-[7vw] py-[54px] text-center" aria-label="Brands I've built for">
    <p class="mb-[26px] text-[12.5px] font-semibold uppercase tracking-[0.16em] text-faint">Brands I've built for</p>
    <ul class="mx-auto flex max-w-[980px] list-none flex-wrap items-center justify-center gap-x-[46px] gap-y-[22px] p-0">
        @foreach (config('portfolio.brands') as $brand)
            <li class="group">
                @if ($brand['logo'])
                    <img
                        class="h-[26px] w-auto opacity-55 grayscale brightness-150 transition duration-250 group-hover:opacity-100 group-hover:grayscale-0 group-hover:brightness-100"
                        src="{{ asset($brand['logo']) }}"
                        alt="{{ $brand['name'] }}"
                        loading="lazy"
                    >
                @else
                    <span class="whitespace-nowrap font-display text-[17px] font-semibold tracking-[0.02em] text-soft opacity-62 transition duration-250 group-hover:text-ink group-hover:opacity-100">
                        {{ $brand['name'] }}
                    </span>
                @endif
            </li>
        @endforeach
    </ul>
</section>
