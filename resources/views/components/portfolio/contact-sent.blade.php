@props(['name' => null])

{{-- Rendered server-side after a no-JS submit, and returned as HTML to the
     fetch request so both paths show exactly the same panel. --}}
<div class="flex flex-col items-start gap-[14px] rounded-[20px] border border-accent/25 bg-surface px-9 py-10">
    <span class="grid h-13 w-13 place-items-center rounded-[14px] bg-accent/15 text-accent">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 6 9 17l-5-5" />
        </svg>
    </span>
    <h3 class="font-display text-[23px] font-semibold">Message sent</h3>
    <p class="text-base leading-[1.6] text-soft">
        Thanks{{ $name ? ', '.$name : '' }} — I'll reply to you soon.
    </p>
    {{-- The query string matters: a bare "/#contact" is the URL we are
         already on, so the browser would only scroll and never re-render
         the form. Script intercepts this and restores the form in place. --}}
    <a href="{{ route('home') }}?contact=new#contact" data-send-another class="mt-1.5 inline-block cursor-pointer rounded-[11px] border border-line-strong px-5 py-[11px] text-[14.5px] font-semibold text-ink hover:border-white/30">
        Send another
    </a>
</div>
