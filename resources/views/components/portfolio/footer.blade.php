<footer class="flex flex-wrap items-center justify-between gap-[14px] border-t border-line px-[7vw] py-[38px]">
    <div class="flex items-center gap-[11px] font-display text-[15px] font-bold">
        <x-portfolio.mark small />
        {{ config('portfolio.name') }}
    </div>
    <p class="text-sm text-faint">© {{ date('Y') }} {{ config('portfolio.name') }} · {{ config('portfolio.location') }} · Built with care.</p>
</footer>
