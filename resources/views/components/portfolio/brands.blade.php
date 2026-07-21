<section id="brands" data-reveal class="pf-brands" aria-label="Brands I've built for">
    <p class="pf-brands__label">Brands I've built for</p>
    <ul class="pf-brands__row">
        @foreach (config('portfolio.brands') as $brand)
            <li class="pf-brands__item">
                @if ($brand['logo'])
                    <img class="pf-brands__logo" src="{{ asset($brand['logo']) }}" alt="{{ $brand['name'] }}" loading="lazy">
                @else
                    <span class="pf-brands__wordmark">{{ $brand['name'] }}</span>
                @endif
            </li>
        @endforeach
    </ul>
</section>
