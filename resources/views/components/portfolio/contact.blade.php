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

        @if (session('contact.sent'))
            <div class="flex flex-col items-start gap-[14px] rounded-[20px] border border-accent/25 bg-surface px-9 py-10">
                <span class="grid h-13 w-13 place-items-center rounded-[14px] bg-accent/15 text-accent">
                    <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M20 6 9 17l-5-5" />
                    </svg>
                </span>
                <h3 class="font-display text-[23px] font-semibold">Message sent</h3>
                <p class="text-base leading-[1.6] text-soft">
                    Thanks, {{ session('contact.sent') }} — I'll reply to you soon.
                </p>
                <a href="{{ route('home') }}#contact" class="mt-1.5 inline-block cursor-pointer rounded-[11px] border border-line-strong px-5 py-[11px] text-[14.5px] font-semibold text-ink hover:border-white/30">
                    Send another
                </a>
            </div>
        @else
            <form method="POST" action="{{ route('contact.send') }}" novalidate class="flex flex-col gap-[18px]">
                @csrf
                @if (session('contact.failed'))
                    <p class="rounded-xl border border-danger/40 bg-danger/10 px-4 py-3 text-[14.5px] leading-[1.6] text-danger">
                        Something went wrong sending your message. Please try again, or email me directly at
                        <a href="mailto:{{ config('portfolio.contact_email') }}" class="font-semibold underline">{{ config('portfolio.contact_email') }}</a>.
                    </p>
                @endif
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
                        placeholder="Tell me about your project…"
                        @class([$field, 'resize-y', 'border-danger' => $errors->has('message'), 'border-white/9' => ! $errors->has('message')])
                    >{{ old('message') }}</textarea>
                    @error('message')
                        <p class="mt-[7px] text-[13px] text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="cursor-pointer self-start rounded-[13px] bg-accent px-7 py-[15px] text-[15.5px] font-bold text-on-accent hover:bg-accent-bright">
                    Send message
                </button>
            </form>
        @endif
    </div>
</section>
