@extends('layouts.app')

@section('title', 'Sign in — ' . config('portfolio.short_name'))

@push('head')
    <meta name="robots" content="noindex, nofollow">
@endpush

@php
    $field = 'w-full rounded-xl border bg-surface px-4 py-[14px] font-sans text-[15.5px] text-ink';
@endphp

@section('content')
    <div class="portfolio flex min-h-screen w-full items-center justify-center bg-bg px-6 py-16 font-sans text-ink antialiased">
        <main id="main" tabindex="-1" class="w-full max-w-[400px]">
            <p class="mb-4 text-sm font-semibold uppercase tracking-[0.14em] text-accent">Admin</p>
            <h1 class="mb-2 font-display text-[34px] font-bold leading-[1.1] tracking-[-0.03em]">Sign in</h1>
            <p class="mb-8 text-[15.5px] leading-[1.6] text-soft">Manage what the portfolio assistant knows.</p>

            <form method="POST" action="{{ route('admin.login.attempt') }}" class="flex flex-col gap-[18px]" novalidate>
                @csrf
                <div>
                    <label for="email" class="mb-2 block text-[13.5px] font-semibold text-muted">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        autofocus
                        required
                        @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
                        @class([$field, 'border-danger' => $errors->has('email'), 'border-line-control' => ! $errors->has('email')])
                    >
                    @error('email')
                        <p id="email-error" class="mt-[7px] text-[13px] text-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label for="password" class="mb-2 block text-[13.5px] font-semibold text-muted">Password</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                        @class([$field, 'border-line-control'])
                    >
                </div>
                <label class="flex items-center gap-2 text-[14px] text-soft">
                    <input type="checkbox" name="remember" value="1" class="h-4 w-4 accent-accent">
                    Keep me signed in
                </label>
                <button type="submit" class="cursor-pointer self-start rounded-[13px] bg-accent px-7 py-[15px] text-[15.5px] font-bold text-on-accent hover:bg-accent-bright">
                    Sign in
                </button>
            </form>

            <p class="mt-10 text-[13.5px] text-faint"><a href="{{ route('home') }}" class="underline hover:text-soft">← Back to the site</a></p>
        </main>
    </div>
@endsection
