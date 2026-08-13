<?php

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public string $password = '';

    /**
     * Delete the currently authenticated user.
     */
    public function deleteUser(Logout $logout): void
    {
        $this->validate([
            'password' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/', navigate: true);
    }
}; ?>

<section class="space-y-6">
    <header>
        <h2 class="text-lg font-semibold text-red-700">
            {{ __t('profile_delete_account_title', 'administration') }}
        </h2>

        <p class="mt-1 text-sm text-primary-600">
            {{ __t('profile_delete_account_description', 'administration') }}
        </p>
    </header>

    <x-danger-button
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
    >{{ __t('profile_delete_account_button', 'administration') }}</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->isNotEmpty()" focusable>
        <form wire:submit="deleteUser" class="p-6 space-y-5">
            <div>
                <h2 class="text-lg font-semibold text-primary-950">
                    {{ __t('profile_delete_account_confirm_title', 'administration') }}
                </h2>

                <p class="mt-1 text-sm text-primary-600">
                    {{ __t('profile_delete_account_confirm_description', 'administration') }}
                </p>
            </div>

            <div>
                <x-input-label for="password" :value="__t('field_password', 'administration')" class="sr-only" />

                <x-text-input
                    wire:model="password"
                    id="password"
                    name="password"
                    type="password"
                    class="block w-full"
                    :placeholder="__t('profile_delete_account_password_placeholder', 'administration')"
                />

                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end gap-2 border-t border-primary-200 pt-5">
                <x-secondary-button x-on:click="$dispatch('close')">
                    {{ __t('cancel', 'administration') }}
                </x-secondary-button>

                <x-danger-button class="ms-2">
                    {{ __t('delete', 'administration') }}
                </x-danger-button>
            </div>
        </form>
    </x-modal>
</section>
