/**
 * Scroll-reveal: fades in `[data-reveal]` elements as they enter the
 * viewport. Purely progressive enhancement — without JavaScript the
 * `pf-motion` class is never added and everything stays visible
 * (see resources/css/app.css).
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

/**
 * reCAPTCHA v3: attach a score token to the contact form on submit.
 *
 * Every failure path still submits the form — a blocked script, an offline
 * visitor or a Google outage must never stop someone contacting Miguel. The
 * server treats a missing token as "unverified" rather than as spam.
 */
const form = document.querySelector('form[data-recaptcha-key]');

if (form && form.recaptcha_token) {
    const siteKey = form.dataset.recaptchaKey;
    const action = form.dataset.recaptchaAction || 'submit';
    let submitting = false;

    form.addEventListener('submit', (event) => {
        if (submitting) {
            return;
        }

        event.preventDefault();

        // Guarded here, not just in the handler: the failsafe timer and the
        // grecaptcha promise can both reach this, and a double form.submit()
        // would deliver the message twice.
        const send = () => {
            if (submitting) {
                return;
            }

            submitting = true;
            form.submit();
        };

        // Never let a hanging script block the visitor.
        const failsafe = window.setTimeout(send, 4000);

        try {
            grecaptcha.ready(() => {
                grecaptcha
                    .execute(siteKey, { action })
                    .then((token) => {
                        form.recaptcha_token.value = token;
                    })
                    .catch(() => {})
                    .finally(() => {
                        window.clearTimeout(failsafe);
                        send();
                    });
            });
        } catch {
            window.clearTimeout(failsafe);
            send();
        }
    });
}
