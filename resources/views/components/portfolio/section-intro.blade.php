@props(['eyebrow', 'title', 'lead'])

<div class="pf-section-intro">
    <p class="pf-eyebrow">{{ $eyebrow }}</p>
    <h2 class="pf-heading">{{ $title }}</h2>
    <p class="pf-lead">{{ $lead }}</p>
    {{ $slot }}
</div>
