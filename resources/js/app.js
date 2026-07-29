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
 * page load, and swap between the form and the sent confirmation in place.
 *
 * All of this is enhancement — with JavaScript off the form posts normally,
 * and the server runs the same validation and renders the same panels.
 */
const panel = document.querySelector('[data-contact-panel]');

if (panel) {
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

    // Captured while the form is still on the page so "Send another" can
    // restore it instantly. When the panel is already showing the sent
    // state (a submit that happened without JavaScript) there is nothing to
    // restore, and the link is left to navigate on its own.
    const pristinePanel = panel.querySelector('[data-contact-form]') ? panel.innerHTML : null;

    const setError = (form, name, message) => {
        const input = form.elements[name];
        if (!input) return;

        input.classList.toggle('border-danger', Boolean(message));
        input.classList.toggle('border-white/9', !message);
        input.setAttribute('aria-invalid', message ? 'true' : 'false');

        input.parentElement.querySelector('[data-error]')?.remove();

        if (message) {
            const p = document.createElement('p');
            p.dataset.error = name;
            p.className = 'mt-[7px] text-[13px] text-danger';
            p.textContent = message;
            input.parentElement.appendChild(p);
        }
    };

    const recaptchaToken = (form) => {
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
                    grecaptcha.execute(siteKey, { action }).then(done).catch(() => done(null));
                });
            } catch {
                done(null);
            }
        });
    };

    const showForm = () => {
        panel.innerHTML = pristinePanel;
        bindForm();
        panel.querySelector('[name="name"]')?.focus();
    };

    const bindSendAnother = () => {
        if (!pristinePanel) return;

        panel.querySelector('[data-send-another]')?.addEventListener('click', (event) => {
            event.preventDefault();
            showForm();
        });
    };

    function bindForm() {
        const form = panel.querySelector('[data-contact-form]');
        if (!form) return;

        const formError = form.querySelector('[data-form-error]');
        const button = form.querySelector('button[type="submit"]');

        const validate = () => {
            let firstInvalid = null;

            fieldNames.forEach((name) => {
                const input = form.elements[name];
                const message = input ? rules[name](input.value) : '';
                setError(form, name, message);
                if (message && !firstInvalid) firstInvalid = input;
            });

            return firstInvalid;
        };

        // Clear a field's error once it is corrected, but never raise a new
        // one while the visitor is still typing their first attempt.
        fieldNames.forEach((name) => {
            const input = form.elements[name];

            input?.addEventListener('input', () => {
                if (input.getAttribute('aria-invalid') === 'true' && !rules[name](input.value)) {
                    setError(form, name, '');
                }
            });
        });

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
                const token = await recaptchaToken(form);
                if (token) form.recaptcha_token.value = token;

                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                });

                if (response.status === 422) {
                    const { errors = {} } = await response.json();
                    fieldNames.forEach((name) => setError(form, name, errors[name]?.[0] || ''));
                    form.elements[Object.keys(errors)[0]]?.focus();

                    return;
                }

                if (!response.ok) {
                    formError.classList.remove('hidden');

                    return;
                }

                const { html } = await response.json();
                panel.innerHTML = html;
                bindSendAnother();
                panel.querySelector('h3')?.scrollIntoView({ block: 'center', behavior: 'smooth' });
            } catch {
                formError.classList.remove('hidden');
            } finally {
                if (document.body.contains(button)) busy(false);
            }
        });
    }

    bindForm();
    bindSendAnother();
}
