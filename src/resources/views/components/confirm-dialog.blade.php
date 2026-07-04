@props([
    'name',
    'title' => 'Delete record',
    'description' => 'This action cannot be undone.',
    'confirmLabel' => 'Delete',
    'cancelLabel' => 'Cancel',
    'action' => null,
    'maxWidth' => 'md',
])


@php
    $buttonAttributes = $action
        ? ['wire:click' => $action]
        : [];
@endphp

<x-modal :name="$name" :max-width="$maxWidth" :title="$title" :description="$description">
    <div class="space-y-5 p-6">
        <div class="flex gap-4 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-red-100 text-red-700">
                <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v4m0 4h.01M10.3 4.6 2.8 18a2 2 0 0 0 1.7 3h15a2 2 0 0 0 1.7-3L13.7 4.6a2 2 0 0 0-3.4 0Z"/></svg>
            </div>
            <div class="min-w-0">
                <p class="text-sm font-semibold text-red-900">{{ $title }}</p>
                <p class="mt-1 text-sm leading-6 text-red-700">{{ $slot->isEmpty() ? $description : $slot }}</p>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-2 border-t border-primary-200 pt-5 sm:flex-row sm:justify-end">
            <x-button variant="secondary" x-on:click="$dispatch('close-modal', '{{ $name }}')">{{ $cancelLabel }}</x-button>
            <x-button
                variant="danger"
                {{ $attributes->merge($buttonAttributes) }}
                wire:loading.attr="disabled"
            >
                <span wire:loading.remove>{{ $confirmLabel }}</span>
                <span wire:loading>Processing...</span>
            </x-button>
        </div>
    </div>
</x-modal>
