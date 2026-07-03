@props(['position' => 'top-right', 'duration' => 4000])

@php
    $positions = [
        'top-right' => 'right-4 top-4 sm:right-6 sm:top-6',
        'top-left' => 'left-4 top-4 sm:left-6 sm:top-6',
        'bottom-right' => 'bottom-4 right-4 sm:bottom-6 sm:right-6',
        'bottom-left' => 'bottom-4 left-4 sm:bottom-6 sm:left-6',
    ];
@endphp

<div
    x-data="{
        toasts: [],
        push(toast) {
            const id = toast.id ?? Date.now() + Math.random();
            const item = {
                id,
                type: toast.type ?? 'success',
                title: toast.title ?? null,
                message: toast.message ?? '',
                duration: toast.duration ?? {{ (int) $duration }},
            };

            this.toasts.push(item);

            if (item.duration > 0) {
                setTimeout(() => this.remove(id), item.duration);
            }
        },
        remove(id) {
            this.toasts = this.toasts.filter((toast) => toast.id !== id);
        },
        styles(type) {
            return {
                success: 'border-accent-200 bg-white text-accent-700',
                info: 'border-sky-200 bg-white text-sky-700',
                warning: 'border-amber-200 bg-white text-amber-700',
                danger: 'border-red-200 bg-white text-red-700',
            }[type] ?? 'border-primary-200 bg-white text-primary-700';
        },
        icon(type) {
            return {
                success: 'M9 12.8 11.2 15 16 9',
                info: 'M12 8h.01M11 12h1v4h1',
                warning: 'M12 9v4m0 4h.01',
                danger: 'M6 18 18 6M6 6l12 12',
            }[type] ?? 'M12 8h.01M11 12h1v4h1';
        },
    }"
    x-on:toast.window="push($event.detail)"
    class="pointer-events-none fixed z-[60] w-[calc(100%-2rem)] max-w-sm space-y-3 {{ $positions[$position] ?? $positions['top-right'] }}"
    aria-live="polite"
    aria-atomic="true"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transform ease-out duration-200"
            x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
            x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="pointer-events-auto overflow-hidden rounded-xl border shadow-lg shadow-primary-900/10"
            x-bind:class="styles(toast.type)"
        >
            <div class="flex gap-3 p-4">
                <div class="grid h-8 w-8 shrink-0 place-items-center rounded-xl bg-current/10">
                    <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                        <path stroke-linecap="round" stroke-linejoin="round" x-bind:d="icon(toast.type)" />
                    </svg>
                </div>

                <div class="min-w-0 flex-1">
                    <p x-show="toast.title" class="text-sm font-semibold text-primary-950" x-text="toast.title"></p>
                    <p class="text-sm text-primary-600" x-bind:class="{ 'mt-0.5': toast.title }" x-text="toast.message"></p>
                </div>

                <button type="button" x-on:click="remove(toast.id)" class="rounded-lg p-1.5 text-primary-400 hover:bg-primary-100 hover:text-primary-700">
                    <span class="sr-only">Close notification</span>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </div>
    </template>
</div>
