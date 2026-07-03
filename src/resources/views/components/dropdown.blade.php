@props(['align' => 'right', 'width' => '56', 'contentClasses' => 'py-1 bg-white'])

@php
    $alignmentClasses = match ($align) {
        'left' => 'origin-top-left left-0',
        'top' => 'origin-top',
        default => 'origin-top-right right-0',
    };

    $width = match ($width) {
        '48' => 'w-48',
        '56' => 'w-56',
        '64' => 'w-64',
        default => $width,
    };
@endphp

<div class="relative" x-data="{ open: false }" x-on:click.outside="open = false" x-on:close.stop="open = false">
    <div x-on:click="open = ! open">{{ $trigger }}</div>

    <div
        x-show="open"
        x-transition
        class="absolute z-50 mt-2 {{ $width }} rounded-xl border border-primary-200 shadow-lg {{ $alignmentClasses }}"
        style="display: none;"
        x-on:click="open = false"
    >
        <div class="rounded-xl {{ $contentClasses }}">
            {{ $content }}
        </div>
    </div>
</div>
