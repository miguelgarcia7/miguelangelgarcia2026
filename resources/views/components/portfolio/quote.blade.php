@props(['author'])

<p {{ $attributes->merge(['class' => 'flex items-center gap-3 font-display text-[16.5px] font-medium italic leading-[1.4] text-muted']) }}>
    <span class="h-0.5 w-[22px] flex-none bg-accent" aria-hidden="true"></span>
    <span>“{{ $slot }}” <span class="font-semibold not-italic text-faint">— {{ $author }}</span></span>
</p>
