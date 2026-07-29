<section id="contact" data-reveal class="pf-section">
    <div class="pf-contact__inner">
        <div>
            <p class="pf-eyebrow">Contact</p>
            <h2 class="pf-heading pf-contact__title">Let's connect.</h2>
            <p class="pf-contact__copy">
                Whether it's a role, a question, or just to talk shop — send a note and I'll
                get back to you soon.
            </p>
        </div>

        @if (session('contact.sent'))
            <div class="pf-contact__success">
                <span class="pf-contact__success-icon">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 6 9 17l-5-5" />
                    </svg>
                </span>
                <h3 class="pf-contact__success-title">Message sent</h3>
                <p class="pf-contact__success-copy">
                    Thanks, {{ session('contact.sent') }} — I'll reply to you soon.
                </p>
                <a href="{{ route('home') }}#contact" class="pf-contact__again">Send another</a>
            </div>
        @else
            <form method="POST" action="{{ route('contact.send') }}" novalidate class="pf-contact__form">
                @csrf
                <div>
                    <label for="contact-name" class="pf-field__label">Name</label>
                    <input
                        id="contact-name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Your name"
                        class="pf-field__input @error('name') is-invalid @enderror"
                    >
                    @error('name')
                        <p class="pf-field__error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contact-email" class="pf-field__label">Email</label>
                    <input
                        id="contact-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@email.com"
                        class="pf-field__input @error('email') is-invalid @enderror"
                    >
                    @error('email')
                        <p class="pf-field__error">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contact-message" class="pf-field__label">Message</label>
                    <textarea
                        id="contact-message"
                        name="message"
                        rows="5"
                        placeholder="Tell me about your project…"
                        class="pf-field__input @error('message') is-invalid @enderror"
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="pf-field__error">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="pf-btn pf-btn--primary pf-contact__submit">Send message</button>
            </form>
        @endif
    </div>
</section>
