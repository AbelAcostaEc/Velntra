@props([
    'viewHref' => null,
    'editHref' => null,
    'deleteModal' => null,
    'align' => 'right',
])

<x-dropdown :align="$align" width="48">
    <x-slot:trigger>
        <button type="button" class="inline-grid h-9 w-9 place-items-center rounded-xl text-primary-500 hover:bg-primary-100 hover:text-primary-900">
            <span class="sr-only">Open actions</span>
            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6.5h.01M12 12h.01M12 17.5h.01"/></svg>
        </button>
    </x-slot:trigger>

    <x-slot:content>
        @isset($items)
            {{ $items }}
        @else
            <a href="{{ $viewHref ?? '#' }}" class="flex items-center gap-2 px-4 py-2 text-sm text-primary-600 hover:bg-primary-50 hover:text-primary-900">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M2.5 12s3.5-6 9.5-6 9.5 6 9.5 6-3.5 6-9.5 6-9.5-6-9.5-6Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6Z"/></svg>
                View
            </a>
            <a href="{{ $editHref ?? '#' }}" class="flex items-center gap-2 px-4 py-2 text-sm text-primary-600 hover:bg-primary-50 hover:text-primary-900">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m16.5 4.5 3 3L8 19H5v-3L16.5 4.5Z"/></svg>
                Edit
            </a>
            @if ($deleteModal)
                <button type="button" x-on:click="$dispatch('open-modal', '{{ $deleteModal }}')" class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16m-9 4v6m4-6v6M9 7l1-2h4l1 2m-9 0 1 13h10l1-13"/></svg>
                    Delete
                </button>
            @endif
        @endisset
    </x-slot:content>
</x-dropdown>
