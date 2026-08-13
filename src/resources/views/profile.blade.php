<x-app-layout>
    <x-slot name="header">
        <x-page-header
            :title="__t('profile_title', 'administration')"
            :description="__t('profile_description', 'administration')"
        />
    </x-slot>

    <div class="space-y-6">
        <div class="rounded-2xl border border-primary-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.update-profile-information-form />
            </div>
        </div>

        <div class="rounded-2xl border border-primary-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.update-password-form />
            </div>
        </div>

        <div class="rounded-2xl border border-red-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="max-w-xl">
                <livewire:profile.delete-user-form />
            </div>
        </div>
    </div>
</x-app-layout>
