@php
    $field = 'w-full rounded-xl border bg-surface px-4 py-[14px] font-sans text-[15.5px] text-ink focus:outline-none';
@endphp

<section id="contact" data-reveal class="border-t border-line px-[7vw] pb-[100px] pt-24">
    {{-- max-w matches the stack groups above so both sections align. --}}
    <div class="mx-auto grid max-w-[1040px] items-start gap-14 max-[900px]:grid-cols-1 max-[900px]:gap-10 min-[901px]:grid-cols-[0.9fr_1.1fr]">
        <div>
            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.14em] text-accent">Contact</p>
            <h2 class="mb-[15px] font-display text-[clamp(30px,3.6vw,46px)] font-bold leading-[1.08] tracking-[-0.03em]">Let's connect.</h2>
            <p class="text-[17px] leading-[1.7] text-body">
                Whether it's a role, a question, or just to talk shop — send a note and I'll
                get back to you soon.
            </p>
        </div>

        {{-- JavaScript swaps this panel's contents on success; without
             JavaScript the server re-renders the page and fills it. --}}
        <div data-contact-panel>
        @if (session('contact.sent'))
            <x-portfolio.contact-sent :name="session('contact.sent')" />
        @else
            <form
                method="POST"
                action="{{ route('contact.send') }}"
                novalidate
                data-contact-form
                class="flex flex-col gap-[18px]"
                @if (config('services.recaptcha.site_key'))
                    data-recaptcha-key="{{ config('services.recaptcha.site_key') }}"
                    data-recaptcha-action="contact"
                @endif
            >
                @csrf
                <input type="hidden" name="recaptcha_token" value="">

                {{-- Filled server-side after a no-JS failure, and by script
                     when a fetch submit cannot be delivered. --}}
                <div data-form-error class="@if (! session('contact.failed')) hidden @endif">
                    <p class="rounded-xl border border-danger/40 bg-danger/10 px-4 py-3 text-[14.5px] leading-[1.6] text-danger">
                        Something went wrong sending your message. Please try again, or email me directly at
                        <a href="mailto:{{ config('portfolio.contact_email') }}" class="font-semibold underline">{{ config('portfolio.contact_email') }}</a>.
                    </p>
                </div>
                <div>
                    <label for="contact-name" class="mb-2 block text-[13.5px] font-semibold text-muted">Name</label>
                    <input
                        id="contact-name"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Your name"
                        @class([$field, 'border-danger' => $errors->has('name'), 'border-white/9' => ! $errors->has('name')])
                    >
                    @error('name')
                        <p class="mt-[7px] text-[13px] text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contact-email" class="mb-2 block text-[13.5px] font-semibold text-muted">Email</label>
                    <input
                        id="contact-email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        placeholder="you@email.com"
                        @class([$field, 'border-danger' => $errors->has('email'), 'border-white/9' => ! $errors->has('email')])
                    >
                    @error('email')
                        <p class="mt-[7px] text-[13px] text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="contact-message" class="mb-2 block text-[13.5px] font-semibold text-muted">Message</label>
                    <textarea
                        id="contact-message"
                        name="message"
                        rows="5"
                        placeholder="What's on your mind?"
                        @class([$field, 'resize-y', 'border-danger' => $errors->has('message'), 'border-white/9' => ! $errors->has('message')])
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-[7px] text-[13px] text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="cursor-pointer self-start rounded-[13px] bg-accent px-7 py-[15px] text-[15.5px] font-bold text-on-accent hover:bg-accent-bright disabled:cursor-wait disabled:opacity-70">
                    Send message
                </button>
                @if (config('services.recaptcha.site_key'))
                    {{-- Required wording when the reCAPTCHA badge is hidden. --}}
                    <p class="text-[12.5px] leading-[1.5] text-faint">
                        Protected by reCAPTCHA — the Google
                        <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="underline hover:text-soft">Privacy Policy</a>
                        and
                        <a href="https://policies.google.com/terms" target="_blank" rel="noopener" class="underline hover:text-soft">Terms of Service</a>
                        apply.
                    </p>
                @endif
            </form>
        @endif
        </div>
    </div>
</section>
