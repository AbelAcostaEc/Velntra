@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
])

@php
    $base = 'inline-flex items-center justify-center gap-2 rounded-xl border font-semibold transition focus:outline-none focus:ring-2 focus:ring-accent-500 focus:ring-offset-2 disabled:pointer-events-none disabled:opacity-50';
    $sizes = [
        'sm' => 'h-9 px-3 text-xs',
        'md' => 'h-10 px-4 text-sm',
        'lg' => 'h-11 px-5 text-sm',
        'icon' => 'h-10 w-10 p-0',
    ];
    $variants = [
        'primary' => 'border-primary-900 bg-primary-900 text-white hover:bg-primary-800',
        'secondary' => 'border-primary-200 bg-white text-primary-700 hover:bg-primary-100',
        'accent' => 'border-accent-600 bg-accent-600 text-white hover:bg-accent-700',
        'danger' => 'border-red-600 bg-red-600 text-white hover:bg-red-700',
        'ghost' => 'border-transparent bg-transparent text-primary-600 hover:bg-primary-100 hover:text-primary-900',
    ];
@endphp

<button type="{{ $type }}" {{ $attributes->merge(['class' => $base.' '.($sizes[$size] ?? $sizes['md']).' '.($variants[$variant] ?? $variants['primary'])]) }}>
    {{ $slot }}
</button>
