@props(['stroke' => 1.5, 'class' => 'w-5 h-5'])
<svg xmlns="http://www.w3.org/2000/svg" {{ $attributes->merge(['class' => $class]) }} fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="{{ $stroke }}" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
</svg>
