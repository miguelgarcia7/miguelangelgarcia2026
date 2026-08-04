<nav class="sticky top-0 z-50 flex items-center justify-between border-b border-line bg-bg/72 px-[7vw] py-[18px] backdrop-blur-[14px]">
    <a href="#top" class="flex items-center gap-[11px] font-display text-[17px] font-bold tracking-[-0.02em] text-ink">
        <x-portfolio.mark />
        {{ config('portfolio.short_name') }}
    </a>
    <div class="flex items-center gap-[34px]">
        {{-- py-1 keeps each link at least 24px tall, the WCAG 2.5.8
             minimum target size; the negative margin keeps the visual
             spacing identical to the design. --}}
        <div class="-my-1 hidden gap-[30px] text-[14.5px] font-medium text-muted min-[721px]:flex">
            <a href="#about" class="py-1 hover:text-ink">About</a>
            <a href="#experience" class="py-1 hover:text-ink">Experience</a>
            <a href="#projects" class="py-1 hover:text-ink">Projects</a>
            <a href="#stack" class="py-1 hover:text-ink">Stack</a>
        </div>
        <a href="#contact" class="rounded-[11px] bg-accent px-5 py-[10px] text-[14.5px] font-bold text-on-accent hover:bg-accent-bright">
            Contact
        </a>
    </div>
</nav>
