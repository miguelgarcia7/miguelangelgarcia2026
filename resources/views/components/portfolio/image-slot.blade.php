@props(['label', 'src' => null, 'alt' => ''])

<div class="absolute inset-0 grid place-items-center bg-slot">
    @if ($src)
        <img class="block h-full w-full object-cover" src="{{ asset($src) }}" alt="{{ $alt }}" loading="lazy">
    @else
        <div class="flex flex-col items-center gap-2.5 text-[13.5px] font-medium text-faint" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <circle cx="9" cy="9" r="2" />
                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
            </svg>
            {{ $label }}
        </div>
    @endif
</div>
