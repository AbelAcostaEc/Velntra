@props(['name' => 'User', 'src' => null, 'size' => 'md'])

@php
    $sizes = ['sm' => 'h-8 w-8 text-xs', 'md' => 'h-10 w-10 text-sm', 'lg' => 'h-12 w-12 text-base'];
    $initials = collect(explode(' ', trim($name)))->filter()->take(2)->map(fn ($part) => mb_substr($part, 0, 1))->implode('');
@endphp

@if ($src)
    <img src="{{ $src }}" alt="{{ $name }}" {{ $attributes->merge(['class' => 'rounded-full object-cover '.($sizes[$size] ?? $sizes['md'])]) }}>
@else
    <span {{ $attributes->merge(['class' => 'inline-grid place-items-center rounded-full bg-primary-900 font-semibold uppercase text-white '.($sizes[$size] ?? $sizes['md'])]) }}>
        {{ $initials ?: 'U' }}
    </span>
@endif
