@props(['variant' => 'info', 'title' => null])

@php
    $variants = [
        'info' => 'border-sky-200 bg-sky-50 text-sky-800',
        'success' => 'border-accent-200 bg-accent-50 text-accent-800',
        'warning' => 'border-amber-200 bg-amber-50 text-amber-800',
        'danger' => 'border-red-200 bg-red-50 text-red-800',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border p-4 text-sm '.($variants[$variant] ?? $variants['info'])]) }}>
    @if ($title)
        <p class="font-semibold">{{ $title }}</p>
    @endif
    <div class="{{ $title ? 'mt-1' : '' }}">{{ $slot }}</div>
</div>
