@props(['label', 'src' => null, 'alt' => ''])

<div class="pf-image-slot">
    @if ($src)
        <img class="pf-image-slot__img" src="{{ asset($src) }}" alt="{{ $alt }}" loading="lazy">
    @else
        <div class="pf-image-slot__placeholder" aria-hidden="true">
            <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                <circle cx="9" cy="9" r="2" />
                <path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21" />
            </svg>
            {{ $label }}
        </div>
    @endif
</div>
