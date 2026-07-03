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
        <div class="grid min-h-screen lg:grid-cols-[1.05fr_0.95fr]">
            <section class="relative hidden overflow-hidden border-r border-primary-200 bg-primary-950 text-white lg:flex lg:flex-col">
                <div class="absolute inset-0 opacity-[0.06]" style="background-image: linear-gradient(rgb(255 255 255 / 0.5) 1px, transparent 1px), linear-gradient(90deg, rgb(255 255 255 / 0.5) 1px, transparent 1px); background-size: 40px 40px;"></div>

                <div class="relative flex flex-1 flex-col justify-between p-10 xl:p-12">
                    <a href="/" wire:navigate class="inline-flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-xl bg-white text-base font-bold text-primary-950">V</span>
                        <span class="text-lg font-semibold tracking-tight">Velntra</span>
                    </a>

                    <div class="max-w-xl">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/10 bg-white/5 px-3 py-1 text-sm text-primary-200">
                            <span class="h-1.5 w-1.5 rounded-full bg-accent-400"></span>
                            POS and inventory operations
                        </div>

                        <h1 class="text-4xl font-semibold tracking-tight text-white xl:text-5xl">
                            Control your store with a calm, reliable back office.
                        </h1>
                        <p class="mt-5 text-base leading-7 text-primary-300">
                            Velntra keeps sales, inventory, customers and reporting in one focused workspace for daily retail operations.
                        </p>
                    </div>

                    <div class="grid gap-3 text-sm text-primary-200 xl:grid-cols-3">
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="font-semibold text-white">Fast POS</p>
                            <p class="mt-1 text-primary-300">Designed for repeated daily workflows.</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="font-semibold text-white">Inventory clarity</p>
                            <p class="mt-1 text-primary-300">Stock signals that are easy to scan.</p>
                        </div>
                        <div class="rounded-xl border border-white/10 bg-white/[0.04] p-4">
                            <p class="font-semibold text-white">Secure access</p>
                            <p class="mt-1 text-primary-300">Breeze and Livewire-ready screens.</p>
                        </div>
                    </div>
                </div>
            </section>

            <main class="flex min-h-screen flex-col">
                <header class="flex h-16 items-center justify-between px-5 sm:px-8 lg:hidden">
                    <a href="/" wire:navigate class="inline-flex items-center gap-3">
                        <span class="grid h-10 w-10 place-items-center rounded-xl bg-primary-900 text-sm font-bold text-white">V</span>
                        <span class="text-base font-semibold tracking-tight text-primary-950">Velntra</span>
                    </a>

                    <x-language-select name="guest_mobile_locale" display="block" />
                </header>

                <div class="flex flex-1 items-center justify-center px-5 py-8 sm:px-8 lg:px-12">
                    <div class="w-full max-w-md">
                        <div class="mb-8 hidden items-center justify-between lg:flex">
                            <a href="/" wire:navigate class="inline-flex items-center gap-3">
                                <span class="grid h-10 w-10 place-items-center rounded-xl bg-primary-900 text-sm font-bold text-white">V</span>
                                <span class="text-base font-semibold tracking-tight text-primary-950">Velntra</span>
                            </a>

                            <x-language-select name="guest_desktop_locale" display="block" />
                        </div>

                        <div class="rounded-2xl border border-primary-200 bg-white p-6 shadow-sm sm:p-8 [&_.text-gray-600]:text-primary-500 [&_.text-gray-900]:text-primary-950 [&_a]:font-medium [&_a]:text-primary-600 [&_a]:underline-offset-4 [&_a:hover]:text-primary-900">
                            {{ $slot }}
                        </div>

                        <p class="mt-6 text-center text-xs text-primary-400">
                            © {{ date('Y') }} Velntra. Secure access for authorized users.
                        </p>
                    </div>
                </div>
            </main>
        </div>

        <x-toast />
        @livewireScripts
    </body>
</html>
