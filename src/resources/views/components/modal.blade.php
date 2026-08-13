@props(['name', 'show' => false, 'maxWidth' => '2xl', 'title' => null, 'description' => null, 'loadingTarget' => null])

@php
    $maxWidth = [
        'sm' => 'sm:max-w-sm',
        'md' => 'sm:max-w-md',
        'lg' => 'sm:max-w-lg',
        'xl' => 'sm:max-w-xl',
        '2xl' => 'sm:max-w-2xl',
        '3xl' => 'sm:max-w-3xl',
    ][$maxWidth];
@endphp

<div
    x-data="{ show: @js($show) }"
    x-init="$watch('show', value => document.body.classList.toggle('overflow-hidden', value))"
    x-on:open-modal.window="$event.detail == '{{ $name }}' ? show = true : null"
    x-on:close-modal.window="$event.detail == '{{ $name }}' ? show = false : null"
    x-on:keydown.escape.window="show = false"
    x-show="show"
    class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
    style="display: {{ $show ? 'block' : 'none' }};"
>
    <div x-show="show" x-transition.opacity class="fixed inset-0 bg-primary-950/40 backdrop-blur-sm" x-on:click="show = false"></div>

    <div
        x-show="show"
        x-transition
        class="relative mb-6 overflow-hidden rounded-2xl border border-primary-200 bg-white shadow-xl sm:mx-auto sm:w-full {{ $maxWidth }}"
    >
        @if ($title || $description)
            <div class="flex items-start justify-between gap-4 border-b border-primary-200 px-6 py-5">
                <div>
                    @if ($title)<h2 class="text-lg font-semibold text-primary-950">{{ $title }}</h2>@endif
                    @if ($description)<p class="mt-1 text-sm text-primary-500">{{ $description }}</p>@endif
                </div>
                <button type="button" x-on:click="show = false" class="rounded-lg p-2 text-primary-400 hover:bg-primary-100 hover:text-primary-700">
                    <span class="sr-only">Close modal</span>
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        @endif

        <div class="relative">
            @if ($loadingTarget)
                <div
                    wire:loading.flex
                    wire:target="{{ $loadingTarget }}"
                    class="absolute inset-0 z-20 hidden items-center justify-center bg-white/80 backdrop-blur-sm"
                >
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-9 w-9 animate-spin rounded-full border-2 border-primary-200 border-t-accent-600"></div>
                        <span class="text-xs font-medium text-primary-500">{{ __('Cargando...') }}</span>
                    </div>
                </div>
            @endif

            <div @if($loadingTarget) wire:loading.class="opacity-40 pointer-events-none" wire:target="{{ $loadingTarget }}" @else wire:loading.class="opacity-60 pointer-events-none" @endif>
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
