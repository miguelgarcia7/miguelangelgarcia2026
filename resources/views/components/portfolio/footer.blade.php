<footer class="flex flex-wrap items-center justify-between gap-[14px] border-t border-line px-[7vw] py-[38px]">
    <div class="flex items-center gap-[11px] font-display text-[15px] font-bold">
        <x-portfolio.mark small />
        {{ config('portfolio.name') }}
    </div>

    <ul class="order-last flex list-none items-center gap-3 p-0 max-[640px]:order-none">
        @foreach (config('portfolio.social') as $profile)
            <li>
                <a
                    href="{{ $profile['url'] }}"
                    target="_blank"
                    rel="noopener me"
                    title="{{ $profile['name'] }}"
                    class="grid h-9 w-9 place-items-center rounded-[10px] border border-line text-soft transition hover:border-accent/40 hover:text-accent"
                >
                    <span class="sr-only">{{ $profile['name'] }}</span>
                    @if ($profile['name'] === 'GitHub')
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M12 .5A11.5 11.5 0 0 0 .5 12a11.5 11.5 0 0 0 7.86 10.92c.58.1.79-.25.79-.55v-2.1c-3.2.7-3.88-1.37-3.88-1.37-.53-1.34-1.29-1.7-1.29-1.7-1.05-.72.08-.7.08-.7 1.16.08 1.77 1.2 1.77 1.2 1.03 1.77 2.7 1.26 3.36.96.1-.75.4-1.26.73-1.55-2.56-.29-5.25-1.28-5.25-5.7 0-1.26.45-2.29 1.19-3.1-.12-.29-.52-1.46.11-3.05 0 0 .97-.31 3.18 1.18a11 11 0 0 1 5.79 0c2.2-1.49 3.17-1.18 3.17-1.18.63 1.59.23 2.76.12 3.05.74.81 1.18 1.84 1.18 3.1 0 4.43-2.69 5.4-5.26 5.69.41.36.78 1.06.78 2.14v3.17c0 .3.21.66.8.55A11.5 11.5 0 0 0 23.5 12 11.5 11.5 0 0 0 12 .5Z" />
                        </svg>
                    @else
                        <svg width="17" height="17" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                            <path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.04-1.85-3.04-1.85 0-2.14 1.45-2.14 2.94v5.67H9.35V9h3.41v1.56h.05a3.74 3.74 0 0 1 3.37-1.85c3.6 0 4.27 2.37 4.27 5.46v6.28ZM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13ZM7.12 20.45H3.56V9h3.56v11.45ZM22.22 0H1.77C.79 0 0 .77 0 1.72v20.56C0 23.23.79 24 1.77 24h20.45c.98 0 1.78-.77 1.78-1.72V1.72C24 .77 23.2 0 22.22 0Z" />
                        </svg>
                    @endif
                </a>
            </li>
        @endforeach
    </ul>

    <p class="text-sm text-faint">© {{ date('Y') }} {{ config('portfolio.name') }} · {{ config('portfolio.location') }} · Built with care.</p>
</footer>
