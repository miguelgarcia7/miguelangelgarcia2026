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
 * Contact form: validate inline, score with reCAPTCHA, submit without a
 * page load. Everything here is enhancement — with JavaScript off the form
 * posts normally and the server does the same validation and rendering.
 */
const form = document.querySelector('[data-contact-form]');

if (form) {
    const panel = form.closest('[data-contact-panel]');
    const formError = form.querySelector('[data-form-error]');
    const button = form.querySelector('button[type="submit"]');
    const EMAIL = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    // Mirrors App\Http\Requests\ContactRequest, message for message. The
    // server stays the authority; this only saves a round trip.
    const rules = {
        name: (v) => (v.trim() ? '' : 'Please enter your name.'),
        email: (v) => {
            if (!v.trim()) return 'Please enter your email.';
            return EMAIL.test(v.trim()) ? '' : 'That doesn’t look like a valid email.';
        },
        message: (v) => {
            if (!v.trim()) return 'Please write a short message.';
            return v.trim().length >= 10 ? '' : 'A little more detail, please (10+ characters).';
        },
    };

    const fieldNames = Object.keys(rules);
    const fieldOf = (name) => form.elements[name];

    const setError = (name, message) => {
        const input = fieldOf(name);
        if (!input) return;

        input.classList.toggle('border-danger', Boolean(message));
        input.classList.toggle('border-white/9', !message);
        input.setAttribute('aria-invalid', message ? 'true' : 'false');

        const existing = input.parentElement.querySelector('[data-error]');
        if (existing) existing.remove();

        if (message) {
            const p = document.createElement('p');
            p.dataset.error = name;
            p.className = 'mt-[7px] text-[13px] text-danger';
            p.textContent = message;
            input.parentElement.appendChild(p);
        }
    };

    const validate = () => {
        let firstInvalid = null;

        fieldNames.forEach((name) => {
            const input = fieldOf(name);
            const message = input ? rules[name](input.value) : '';
            setError(name, message);
            if (message && !firstInvalid) firstInvalid = input;
        });

        return firstInvalid;
    };

    // Clear a field's error as soon as the visitor fixes it, but never
    // show a new one while they are still typing their first attempt.
    fieldNames.forEach((name) => {
        const input = fieldOf(name);
        if (!input) return;

        input.addEventListener('input', () => {
            if (input.getAttribute('aria-invalid') === 'true' && !rules[name](input.value)) {
                setError(name, '');
            }
        });
    });

    const recaptchaToken = () => {
        const siteKey = form.dataset.recaptchaKey;
        if (!siteKey || typeof grecaptcha === 'undefined') return Promise.resolve(null);

        const action = form.dataset.recaptchaAction || 'submit';

        return new Promise((resolve) => {
            // A hanging or failing reCAPTCHA must not stop the submit; the
            // server treats a missing token as unverified, not as spam.
            const failsafe = window.setTimeout(() => resolve(null), 4000);
            const done = (token) => {
                window.clearTimeout(failsafe);
                resolve(token);
            };

            try {
                grecaptcha.ready(() => {
                    grecaptcha
                        .execute(siteKey, { action })
                        .then(done)
                        .catch(() => done(null));
                });
            } catch {
                done(null);
            }
        });
    };

    const busy = (state) => {
        button.disabled = state;
        button.textContent = state ? 'Sending…' : 'Send message';
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        formError.classList.add('hidden');

        const firstInvalid = validate();
        if (firstInvalid) {
            firstInvalid.focus();
            return;
        }

        busy(true);

        try {
            const token = await recaptchaToken();
            if (token) form.recaptcha_token.value = token;

            const response = await fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            });

            if (response.status === 422) {
                const { errors = {} } = await response.json();
                fieldNames.forEach((name) => setError(name, errors[name]?.[0] || ''));
                fieldOf(Object.keys(errors)[0])?.focus();

                return;
            }

            if (!response.ok) {
                formError.classList.remove('hidden');

                return;
            }

            const { html } = await response.json();
            panel.innerHTML = html;
            panel.querySelector('h3')?.scrollIntoView({ block: 'center', behavior: 'smooth' });
        } catch {
            formError.classList.remove('hidden');
        } finally {
            if (document.body.contains(button)) busy(false);
        }
    });
}
