<section id="projects" data-reveal tabindex="0" aria-labelledby="projects-heading" class="pf-divider isolate px-[7vw] py-24">
    <div class="pf-glow pf-glow--projects" aria-hidden="true"></div>
    <x-portfolio.section-intro
        eyebrow="Selected work"
        title="Projects"
        heading-id="projects-heading"
        lead="A selection of products I've designed and built end-to-end — from interface to backend."
    >
        <x-portfolio.quote author="Steve Jobs">We hire people who want to make the best things in the world.</x-portfolio.quote>
    </x-portfolio.section-intro>
    <div class="mx-auto grid max-w-[1920px] gap-7 max-[680px]:grid-cols-1 min-[681px]:grid-cols-2">
        @foreach (config('portfolio.projects') as $project)
            @php($projectId = 'project-'.\Illuminate\Support\Str::slug($project['title']))
            {{-- Each card is its own tab stop, named by its title so a
                 screen reader announces which project it has landed on. --}}
            <article
                tabindex="0"
                aria-labelledby="{{ $projectId }}-title"
                class="pf-card flex flex-col overflow-hidden rounded-[20px] border border-line"
            >
                <div class="relative aspect-16/11 overflow-hidden">
                    <x-portfolio.image-slot label="Project image" :src="$project['image']" :alt="$project['title']" />
                </div>
                <div class="px-[22px] pb-6 pt-[22px]">
                    <h3 id="{{ $projectId }}-title" class="mb-[9px] font-display text-xl font-semibold tracking-[-0.02em]">{{ $project['title'] }}</h3>
                    <p class="mb-4 text-[14.5px] leading-[1.55] text-soft">{{ $project['description'] }}</p>
                    <div class="flex flex-wrap gap-2">
                        @foreach ($project['tags'] as $tag)
                            <span class="rounded-lg border border-accent/16 bg-[linear-gradient(90deg,rgb(46_230_166/0.13),rgb(34_195_214/0.09))] px-[11px] py-[5px] text-[12.5px] font-semibold text-accent">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
