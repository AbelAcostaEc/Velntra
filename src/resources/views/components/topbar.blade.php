<header {{ $attributes->merge(['class' => 'sticky top-0 z-30 flex h-16 items-center gap-3 border-b border-primary-200 bg-white/85 px-4 backdrop-blur sm:px-6 lg:px-8']) }}>
    <button type="button" x-on:click="sidebarOpen = true" class="rounded-xl p-2 text-primary-500 hover:bg-primary-100 hover:text-primary-900 lg:hidden">
        <span class="sr-only">Open sidebar</span>
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 7h16M4 12h16M4 17h16"/></svg>
    </button>

    <div class="min-w-0 flex-1">
        <x-search-input placeholder="Search products, customers, sales..." class="max-w-xl" />
    </div>

    <x-language-select />

    <x-button variant="ghost" size="icon" aria-label="Notifications">
        <svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.4-1.4A2 2 0 0 1 18 14.2V11a6 6 0 1 0-12 0v3.2a2 2 0 0 1-.6 1.4L4 17h5m6 0a3 3 0 0 1-6 0"/></svg>
    </x-button>

    <x-dropdown width="64">
        <x-slot:trigger>
            <button type="button" class="flex items-center gap-2 rounded-xl p-1.5 text-left hover:bg-primary-100">
                <x-avatar :name="auth()->user()->name ?? 'Admin User'" size="sm" />
                <span class="hidden text-sm font-medium text-primary-700 sm:block">{{ auth()->user()->name ?? 'Admin User' }}</span>
            </button>
        </x-slot:trigger>
        <x-slot:content>
            <div class="px-4 py-3">
                <p class="text-sm font-semibold text-primary-950">{{ auth()->user()->name ?? 'Admin User' }}</p>
                <p class="truncate text-sm text-primary-500">{{ auth()->user()->email ?? 'admin@velntra.test' }}</p>
            </div>
            <div class="border-t border-primary-100 px-4 py-3 sm:hidden">
                <x-language-select name="mobile_locale" display="block" class="w-full" />
            </div>
            <div class="border-t border-primary-100 py-1">
                <a href="#" class="block px-4 py-2 text-sm text-primary-600 hover:bg-primary-50 hover:text-primary-900">Profile</a>
                <a href="#" class="block px-4 py-2 text-sm text-primary-600 hover:bg-primary-50 hover:text-primary-900">Settings</a>
                <a href="{{ route('logout') }}" class="block px-4 py-2 text-sm text-primary-600 hover:bg-primary-50 hover:text-primary-900">Log Out</a>
            </div>
        </x-slot:content>
    </x-dropdown>
</header>
