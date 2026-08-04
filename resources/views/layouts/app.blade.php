<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title', config('portfolio.short_name'))</title>
        <meta name="description" content="@yield('description', config('portfolio.description'))">
        <link rel="canonical" href="{{ url()->current() }}">
        <meta name="theme-color" content="#0c0e11">

        {{-- Open Graph / Twitter --}}
        <meta property="og:site_name" content="{{ config('portfolio.short_name') }}">
        <meta property="og:type" content="website">
        <meta property="og:title" content="@yield('title', config('portfolio.short_name'))">
        <meta property="og:description" content="@yield('description', config('portfolio.description'))">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta name="twitter:card" content="summary">
        {{-- TODO: add a 1200x630 og:image and reference it here --}}
        <link rel="icon" type="image/png" href="/favicon-96x96.png" sizes="96x96" />
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        <link rel="shortcut icon" href="/favicon.ico" />
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png" />
        <link rel="manifest" href="/site.webmanifest" />

        @stack('head')

        @fonts

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @if (config('services.recaptcha.site_key'))
            <script src="https://www.google.com/recaptcha/api.js?render={{ config('services.recaptcha.site_key') }}" async defer></script>
        @endif
    </head>
    <body>
        <a href="#main" class="skip-link">Skip to content</a>
        @yield('content')
    </body>
</html>
