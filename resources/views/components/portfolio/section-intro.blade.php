@props(['eyebrow', 'title', 'lead'])

<div class="mb-12 text-center">
    <p class="mb-[14px] text-sm font-semibold uppercase tracking-[0.14em] text-accent">{{ $eyebrow }}</p>
    <h2 class="mb-[15px] font-display text-[clamp(30px,3.6vw,46px)] font-bold leading-[1.08] tracking-[-0.03em]">{{ $title }}</h2>
    <p class="mx-auto max-w-[520px] text-base leading-[1.6] text-soft">{{ $lead }}</p>
    @if (trim($slot) !== '')
        <div class="mt-[22px] flex justify-center">
            <div class="text-left">{{ $slot }}</div>
        </div>
    @endif
</div>
