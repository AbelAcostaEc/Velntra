@props(['title' => 'No records found', 'description' => 'Create a new record or adjust your filters to see results.'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center px-6 py-12 text-center']) }}>
    <div class="grid h-12 w-12 place-items-center rounded-xl bg-primary-100 text-primary-500">
        @isset($icon)
            {{ $icon }}
        @else
            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h10"/></svg>
        @endif
    </div>
    <h3 class="mt-4 text-sm font-semibold text-primary-950">{{ $title }}</h3>
    <p class="mt-1 max-w-sm text-sm text-primary-500">{{ $description }}</p>
    @isset($action)
        <div class="mt-5">{{ $action }}</div>
    @endisset
</div>
