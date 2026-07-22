<section id="about" data-reveal class="pf-section pf-about">
    <div class="pf-about__photo">
        <div class="pf-about__photo-glow"></div>
        <div class="pf-about__photo-frame">
            <x-portfolio.image-slot label="Drop your photo" :alt="config('portfolio.name')" />
        </div>
    </div>
    <div>
        <p class="pf-eyebrow">About me</p>
        <h2 class="pf-heading pf-about__title">
            The web has changed a lot in 15 years. So have I.
        </h2>
        <x-portfolio.quote author="Steve Jobs">Stay hungry, stay foolish.</x-portfolio.quote>
        <p class="pf-about__text"> I'm a full-stack engineer with more than 15 years of experience building thoughtful, reliable digital products for web and mobile users. My career has evolved from designing and developing websites to architecting complex web applications, leading technical initiatives, and collaborating with engineers, designers, and business stakeholders to turn ideas into scalable solutions. </p>
        <p class="pf-about__text"> I bring a strong appreciation for interface design and user experience, along with a deep focus on architecture, maintainability, accessibility, and delivery. I care not only about what a team builds, but also how it collaborates, makes decisions, and consistently ships high-quality work. I have also mentored aspiring developers through a coding mentorship program at UT Dallas. </p>
        <p class="pf-about__text"> I'm interested in senior engineering opportunities where I can combine hands-on development with technical leadership, architecture, mentorship, and product thinking. That may take the form of an engineering leadership position or a senior individual contributor role, depending on the team and the problems being solved. </p>
        <p class="pf-about__text"> Over the past two years, AI-assisted development has become an important part of my day-to-day workflow. I use tools such as Claude Code and work directly with the Anthropic and OpenAI APIs to accelerate research, prototyping, implementation, and refinement. My two most recent applications, QCP Staffing / Minute and Appointment Hub, were developed end to end using this AI-enabled approach. </p>
        <p class="pf-about__motto">
            Never Stop <span class="pf-about__motto-accent">Learning.</span>
        </p>
        <div class="pf-about__stats">
            <div>
                <div class="pf-about__stat-value">15+</div>
                <div class="pf-about__stat-label">Years experience</div>
            </div>
            <div>
                <div class="pf-about__stat-value">2+</div>
                <div class="pf-about__stat-label">Years of AI-first development</div>
            </div>
            <div>
                <div class="pf-about__stat-value">∞</div>
                <div class="pf-about__stat-label">Always learning</div>
            </div>
        </div>
    </div>
</section>
