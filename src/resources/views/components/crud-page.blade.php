@props(['title', 'description' => null])

<div {{ $attributes->merge(['class' => 'space-y-6']) }}>
    <x-page-header :title="$title" :description="$description">
        @isset($actions)
            <x-slot:actions>{{ $actions }}</x-slot:actions>
        @endisset
    </x-page-header>

    @isset($summary)
        {{ $summary }}
    @endisset

    @isset($content)
        {{ $content }}
    @else
        {{ $slot }}
    @endisset

    @isset($modals)
        {{ $modals }}
    @endisset
</div>
