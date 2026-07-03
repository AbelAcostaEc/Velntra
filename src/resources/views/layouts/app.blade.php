<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Velntra') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="bg-primary-50 font-sans text-primary-900 antialiased">
        <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen">
            <div
                x-cloak
                x-show="sidebarOpen"
                x-transition.opacity
                x-on:click="sidebarOpen = false"
                class="fixed inset-0 z-40 bg-primary-950/30 backdrop-blur-sm lg:hidden"
            ></div>

            <aside
                x-cloak
                class="fixed inset-y-0 left-0 z-50 flex w-72 -translate-x-full flex-col border-r border-primary-200 bg-white transition duration-200 lg:translate-x-0"
                x-bind:class="{ 'translate-x-0': sidebarOpen, 'lg:w-20': sidebarCollapsed, 'lg:w-72': ! sidebarCollapsed }"
            >
                <div class="flex h-16 items-center justify-between border-b border-primary-200 px-4">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3 overflow-hidden">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-xl bg-primary-900 text-sm font-bold text-white">V</span>
                        <span x-show="! sidebarCollapsed" class="truncate text-sm font-semibold tracking-tight text-primary-950">Velntra</span>
                    </a>

                    <button type="button" x-on:click="sidebarOpen = false" class="rounded-lg p-2 text-primary-500 hover:bg-primary-100 hover:text-primary-800 lg:hidden">
                        <span class="sr-only">Close sidebar</span>
                        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <nav class="flex-1 space-y-6 overflow-y-auto px-3 py-5">
                    <div class="space-y-1">
                        <x-sidebar-item href="{{ route('dashboard') }}" label="Dashboard" :active="request()->routeIs('dashboard')">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.2V6.8A2.8 2.8 0 0 1 5.8 4h3.4A2.8 2.8 0 0 1 12 6.8v6.4A2.8 2.8 0 0 1 9.2 16H5.8A2.8 2.8 0 0 1 3 13.2ZM12 17.2v-6.4A2.8 2.8 0 0 1 14.8 8h3.4a2.8 2.8 0 0 1 2.8 2.8v6.4a2.8 2.8 0 0 1-2.8 2.8h-3.4a2.8 2.8 0 0 1-2.8-2.8Z"/></svg>
                        </x-sidebar-item>

                        <x-sidebar-group label="Inventory" :open="request()->is('inventory*')">
                            <x-slot:icon><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7.5 12 3l8 4.5M4 7.5v9L12 21m-8-13.5 8 4.5m8-4.5v9L12 21m8-13.5-8 4.5m0 0V21"/></svg></x-slot:icon>
                            <x-sidebar-item href="#" label="Categories" nested />
                            <x-sidebar-item href="#" label="Products" nested />
                        </x-sidebar-group>

                        <x-sidebar-item href="#" label="Customers">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M16 19a4 4 0 0 0-8 0M12 13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6.5 5.5c.9-.7 1.5-1.8 1.5-3a3.5 3.5 0 0 0-5.2-3.1M5.5 18.5a3.7 3.7 0 0 1-1.5-3 3.5 3.5 0 0 1 5.2-3.1"/></svg>
                        </x-sidebar-item>
                        <x-sidebar-item href="#" label="Sales">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h15l-2 8H8L6 6Zm0 0-.7-3H3m6 18a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Zm9 0a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3Z"/></svg>
                        </x-sidebar-item>
                        <x-sidebar-item href="#" label="Reports">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M5 19V5m0 14h14M9 15v-4m4 4V7m4 8V9"/></svg>
                        </x-sidebar-item>
                        <x-sidebar-item href="#" label="Settings">
                            <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 15.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7Zm7-3.5a7 7 0 0 0-.1-1l2-1.5-2-3.5-2.4 1a7.2 7.2 0 0 0-1.8-1L14.4 3h-4.8l-.3 3a7.2 7.2 0 0 0-1.8 1L5.1 6 3 9.5 5.1 11a7 7 0 0 0 0 2L3 14.5 5.1 18l2.4-1a7.2 7.2 0 0 0 1.8 1l.3 3h4.8l.3-3a7.2 7.2 0 0 0 1.8-1l2.4 1 2.1-3.5-2.1-1.5c.1-.3.1-.7.1-1Z"/></svg>
                        </x-sidebar-item>
                    </div>
                </nav>

                <div class="border-t border-primary-200 p-3">
                    <button type="button" x-on:click="sidebarCollapsed = ! sidebarCollapsed" class="hidden w-full items-center gap-3 rounded-xl px-3 py-2 text-sm font-medium text-primary-600 hover:bg-primary-100 hover:text-primary-900 lg:flex">
                        <svg class="h-5 w-5 transition" x-bind:class="{ 'rotate-180': sidebarCollapsed }" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 6 9 12l6 6"/></svg>
                        <span x-show="! sidebarCollapsed">Collapse</span>
                    </button>
                </div>
            </aside>

            <div class="transition-all duration-200 lg:pl-72" x-bind:class="{ 'lg:pl-20': sidebarCollapsed, 'lg:pl-72': ! sidebarCollapsed }">
                <x-topbar />

                <main class="px-4 py-6 sm:px-6 lg:px-8">
                    @if (isset($header))
                        <div class="mb-6">{{ $header }}</div>
                    @endif

                    {{ $slot }}
                </main>
            </div>

            <x-toast />
        </div>

        @livewireScripts
    </body>
</html>
