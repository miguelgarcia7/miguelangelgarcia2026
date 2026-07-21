/**
 * Scroll-reveal: fades in `[data-reveal]` elements as they enter the
 * viewport. Purely progressive enhancement — without JavaScript the
 * `pf-motion` class is never added and everything stays visible
 * (see resources/css/portfolio/portfolio.css).
 */
const root = document.querySelector('.portfolio');

if (root) {
    const elements = Array.from(root.querySelectorAll('[data-reveal]'));

    if (elements.length > 0) {
        root.classList.add('pf-motion');

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('pf-revealed');
                        observer.unobserve(entry.target);
                    }
                });
            },
            { threshold: 0.12 },
        );

        elements.forEach((element) => observer.observe(element));
    }
}
