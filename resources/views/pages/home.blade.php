@extends('layouts.app')

@section('title', config('portfolio.name') . ' — ' . config('portfolio.job_title'))
@section('description', config('portfolio.description'))

@php
    // Built inside @php so Blade never mistakes schema.org keys like
    // "@context" for Blade directives.
    $structuredData = json_encode([
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'Person',
                '@id' => url('/') . '#person',
                'name' => config('portfolio.name'),
                'jobTitle' => config('portfolio.job_title'),
                'description' => config('portfolio.description'),
                'url' => url('/'),
                'knowsAbout' => collect(config('portfolio.stack_groups'))
                    ->flatMap(fn (array $group) => collect($group['items'])->pluck('name'))
                    ->values()
                    ->all(),
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => 'Dallas–Fort Worth',
                    'addressRegion' => 'TX',
                    'addressCountry' => 'US',
                ],
                'worksFor' => [
                    '@type' => 'Organization',
                    'name' => 'Jonah Digital',
                    'url' => 'https://jonahdigital.com',
                ],
                'sameAs' => config('portfolio.same_as'),
            ],
            [
                '@type' => 'WebSite',
                'name' => config('portfolio.short_name'),
                'url' => url('/'),
                'about' => ['@id' => url('/') . '#person'],
            ],
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
@endphp

@push('head')
    <script type="application/ld+json">{!! $structuredData !!}</script>
@endpush

@section('content')
    {{-- overflow-x-clip (not -hidden) keeps stray decoration from causing
         sideways scroll without making this a scroll container, which would
         break the sticky nav. --}}
    <div id="top" class="portfolio min-h-screen w-full overflow-x-clip bg-bg font-sans text-ink antialiased">
        <x-portfolio.nav />
        <main id="main" tabindex="-1">
            {{-- variant: statement | split | editorial --}}
            <x-portfolio.hero variant="statement" />
            <x-portfolio.about />
            <x-portfolio.experience />
            <x-portfolio.brands />
            <x-portfolio.projects />
            <x-portfolio.stack />
            @if (config('ai.enabled'))
                <x-portfolio.ask />
            @endif
            <x-portfolio.contact />
        </main>
        <x-portfolio.footer />
    </div>
@endsection
