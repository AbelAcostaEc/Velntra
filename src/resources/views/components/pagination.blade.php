@props(['summary' => 'Showing 1 to 10 of 24 results'])

<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
    <p class="text-sm text-primary-500">{{ $summary }}</p>
    <div class="flex items-center gap-2">
        <x-button variant="secondary" size="sm">Previous</x-button>
        <x-button variant="secondary" size="sm">Next</x-button>
    </div>
</div>
