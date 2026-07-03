@props(['href' => '#', 'label', 'active' => false, 'nested' => false])

@php
    $classes = $active
        ? 'bg-primary-900 text-white'
        : 'text-primary-600 hover:bg-primary-100 hover:text-primary-950';
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => 'group flex items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium transition '.$classes.($nested ? ' ml-8' : '')]) }}>
    @if (! $nested)
        <span class="shrink-0">{{ $slot }}</span>
    @endif
    <span x-show="! sidebarCollapsed" class="truncate">{{ $label }}</span>
</a>
