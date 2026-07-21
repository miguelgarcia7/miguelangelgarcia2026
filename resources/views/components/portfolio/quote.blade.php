@props(['author'])

<p {{ $attributes->merge(['class' => 'pf-quote']) }}>
    <span class="pf-quote__dash" aria-hidden="true"></span>
    <span>“{{ $slot }}” <span class="pf-quote__author">— {{ $author }}</span></span>
</p>
