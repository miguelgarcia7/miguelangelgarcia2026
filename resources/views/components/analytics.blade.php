@php
    $measurementId = config('services.google_analytics.measurement_id');
@endphp

{{-- Google tag (gtag.js). Renders only when GOOGLE_ANALYTICS_ID is set,
     so local and test runs are never tracked. --}}
@if ($measurementId)
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ $measurementId }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', @js($measurementId));
    </script>
@endif
