@props(['variant' => 'neutral'])

@php
    $variants = [
        'neutral' => 'bg-primary-100 text-primary-700 ring-primary-200',
        'success' => 'bg-accent-50 text-accent-700 ring-accent-200',
        'warning' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'danger' => 'bg-red-50 text-red-700 ring-red-200',
        'info' => 'bg-sky-50 text-sky-700 ring-sky-200',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset '.($variants[$variant] ?? $variants['neutral'])]) }}>
    {{ $slot }}
</span>
