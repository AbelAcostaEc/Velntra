@props(['label', 'open' => false])

<div x-data="{ open: @js($open) }">
    <button type="button" x-on:click="open = ! open" class="flex w-full items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-primary-600 transition hover:bg-primary-100 hover:text-primary-950">
        <span class="shrink-0">{{ $icon }}</span>
        <span x-show="! sidebarCollapsed" class="flex-1 truncate text-left">{{ $label }}</span>
        <svg x-show="! sidebarCollapsed" class="h-4 w-4 transition" x-bind:class="{ 'rotate-90': open }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6"/></svg>
    </button>

    <div x-show="open && ! sidebarCollapsed" class="mt-1 space-y-1">
        {{ $slot }}
    </div>
</div>
