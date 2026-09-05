<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <meta name="color-scheme" content="dark">
        <title inertia>Admin — {{ config('portfolio.short_name') }}</title>
        <link rel="icon" type="image/svg+xml" href="/favicon.svg" />
        @fonts
        @vite(['resources/css/admin.css', 'resources/js/admin/app.tsx'])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
