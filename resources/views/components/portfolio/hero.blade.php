@props(['variant' => 'statement'])

@if ($variant === 'split')
    <x-portfolio.hero.split />
@elseif ($variant === 'editorial')
    <x-portfolio.hero.editorial />
@else
    <x-portfolio.hero.statement />
@endif
