@props(['center' => false])

<div @class(['flex flex-wrap gap-[14px]', 'justify-center' => $center])>
    <a href="#projects" class="inline-block rounded-[13px] bg-accent px-7 py-[15px] text-[15.5px] font-bold text-on-accent hover:bg-accent-bright">
        View my work
    </a>
    <a href="#contact" class="inline-block rounded-[13px] border border-line-strong px-7 py-[15px] text-[15.5px] font-semibold text-ink hover:border-white/30">
        Get in touch
    </a>
</div>
