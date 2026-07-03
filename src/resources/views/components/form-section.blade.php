@props(['title', 'description' => null])

<x-card>
    <div class="grid gap-6 lg:grid-cols-3">
        <div>
            <h3 class="text-sm font-semibold text-primary-950">{{ $title }}</h3>
            @if ($description)
                <p class="mt-1 text-sm text-primary-500">{{ $description }}</p>
            @endif
        </div>
        <div class="space-y-5 lg:col-span-2">{{ $slot }}</div>
    </div>
</x-card>
