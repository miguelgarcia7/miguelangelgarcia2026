<section id="projects" data-reveal class="pf-section">
    <x-portfolio.section-intro
        eyebrow="Selected work"
        title="Projects"
        lead="A selection of products I've designed and built end-to-end — from interface to backend."
    >
        <x-portfolio.quote author="Steve Jobs">We hire people who want to make the best things in the world.</x-portfolio.quote>
    </x-portfolio.section-intro>
    <div class="pf-projects__grid">
        @foreach (config('portfolio.projects') as $project)
            <article class="pf-project">
                <div class="pf-project__media">
                    <x-portfolio.image-slot label="Project image" :src="$project['image']" :alt="$project['title']" />
                </div>
                <div class="pf-project__body">
                    <h3 class="pf-project__title">{{ $project['title'] }}</h3>
                    <p class="pf-project__desc">{{ $project['description'] }}</p>
                    <div class="pf-project__tags">
                        @foreach ($project['tags'] as $tag)
                            <span class="pf-tag">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            </article>
        @endforeach
    </div>
</section>
