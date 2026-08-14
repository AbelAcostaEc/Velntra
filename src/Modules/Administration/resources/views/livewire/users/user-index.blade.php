<div>
    <x-crud-page :title="__t('users_management', 'administration')" :description="__t('users_management_description', 'administration')">
        <x-slot:actions>
            @can('create', \Modules\Administration\Models\User::class)
                <x-button
                    wire:click="openCreateModal"
                    x-on:click="$dispatch('open-modal', 'user-form')"
                    wire:loading.attr="disabled">
                    <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __t('create_user', 'administration') }}
                </x-button>
            @endcan
        </x-slot:actions>

        <x-slot:summary>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <x-stat-card :label="__t('total_users', 'administration')" value="{{ $totalUsers }}" :trend="__t('registered', 'administration')" variant="info" />
                <x-stat-card :label="__t('admins', 'administration')" value="{{ $adminCount }}" :trend="__t('full_access', 'administration')" variant="success" />
                <x-stat-card :label="__t('sellers', 'administration')" value="{{ $sellerCount }}" :trend="__t('operational_access', 'administration')" variant="warning" />
            </div>
        </x-slot:summary>

        <x-slot:content>
            <x-table loading-target="search, perPage">
                <x-slot:toolbar>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between w-full">
                        <x-table-search :placeholder="__t('search_placeholder', 'administration')" wire:model.live.debounce.300ms="search" class="w-full sm:max-w-xs" />
                        <x-per-page-select wire:model.live="perPage" />
                    </div>
                </x-slot:toolbar>

                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_name', 'administration') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_email', 'administration') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_role', 'administration') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_actions', 'administration') }}</th>
                    </tr>
                </x-slot:head>

                @forelse($users as $user)
                    <tr class="hover:bg-primary-50/50 transition-colors">
                        <td class="px-4 py-4 text-sm font-medium text-primary-950">{{ $user->name }}</td>
                        <td class="px-4 py-4 text-sm text-primary-600">{{ $user->email }}</td>
                        <td class="px-4 py-4">
                            @foreach ($user->roles as $r)
                                <x-badge :variant="$r->name === 'admin' ? 'info' : 'success'">
                                    {{ ucfirst($r->name) }}
                                </x-badge>
                            @endforeach
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $user)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $user->id }})"
                                        x-on:click="$dispatch('open-modal', 'user-form')">
                                        {{ __t('edit', 'administration') }}
                                    </x-button>
                                @endcan
                                @can('delete', $user)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $user->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-user')">
                                        {{ __t('delete', 'administration') }}
                                    </x-button>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <x-empty-state
                                :title="__t('no_users_title', 'administration')"
                                :description="__t('no_users_description', 'administration')" />
                        </td>
                    </tr>
                @endforelse

                <x-slot:mobile>
                    @foreach ($users as $user)
                        <x-mobile-record-card
                            :title="$user->name"
                            :subtitle="$user->email">
                            <x-slot:status>
                                @forelse ($user->roles as $r)
                                    <x-badge :variant="$r->name === 'admin' ? 'info' : 'success'">
                                        {{ ucfirst($r->name) }}
                                    </x-badge>
                                @empty
                                    <x-badge variant="neutral">
                                        {{ __t('no_role', 'administration') }}
                                    </x-badge>
                                @endforelse
                            </x-slot:status>

                            <x-slot:actions>
                                @can('update', $user)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $user->id }})"
                                        x-on:click="$dispatch('open-modal', 'user-form')">
                                        {{ __t('edit', 'administration') }}
                                    </x-button>
                                @endcan
                                @can('delete', $user)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $user->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-user')">
                                        {{ __t('delete', 'administration') }}
                                    </x-button>
                                @endcan
                            </x-slot:actions>
                        </x-mobile-record-card>
                    @endforeach
                </x-slot:mobile>

                <x-slot:pagination>
                    <div class="px-4 py-3">
                        {{ $users->links() }}
                    </div>
                </x-slot:pagination>
            </x-table>
        </x-slot:content>
    </x-crud-page>

    {{-- Modal Formulario Crear / Editar --}}
    <x-modal
        name="user-form"
        :title="$selectedUserId ? __t('edit_user', 'administration') : __t('create_user', 'administration')"
        :description="__t('form_description', 'administration')"
        loading-target="openCreateModal, openEditModal">
        <form wire:submit.prevent="save" class="space-y-5 p-6">
            <x-input
                :label="__t('field_name', 'administration')"
                name="name"
                :placeholder="__t('field_name_placeholder', 'administration')"
                wire:model="name"
                required />

            <x-input
                :label="__t('field_email', 'administration')"
                name="email"
                type="email"
                placeholder="juan@ejemplo.com"
                wire:model="email"
                required />

            <x-input
                :label="__t('field_password', 'administration')"
                name="password"
                type="password"
                :placeholder="$selectedUserId ? __t('field_password_edit_placeholder', 'administration') : __t('field_password_create_placeholder', 'administration')"
                wire:model="password" />

            {{-- Roles (checkboxes - multiple) --}}
            <div>
                <label class="block text-sm font-medium text-primary-700 mb-2">
                    {{ __t('field_role', 'administration') }}
                    <span class="text-red-500 ml-0.5">*</span>
                </label>
                <div class="flex flex-wrap gap-3">
                    @foreach ($roles as $r)
                        <label class="flex items-center gap-2 cursor-pointer select-none group">
                            <input
                                type="checkbox"
                                value="{{ $r->name }}"
                                wire:model="selectedRoles"
                                class="h-4 w-4 rounded border-primary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                            />
                            <span class="text-sm text-primary-700 group-hover:text-primary-900 transition-colors">
                                {{ ucfirst($r->name) }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('selectedRoles')
                    <p class="mt-1.5 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2 border-t border-primary-200 pt-5">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'user-form')">
                    {{ __t('cancel', 'administration') }}
                </x-button>

                <x-button type="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove>{{ __t('save', 'administration') }}</span>
                    <span wire:loading>{{ __t('saving', 'administration') }}</span>
                </x-button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Confirmación de Eliminación --}}
    <x-confirm-dialog
        name="delete-user"
        :title="__t('delete_user_title', 'administration')"
        :description="__t('delete_user_description', 'administration')"
        :confirm-label="__t('delete', 'administration')"
        :cancel-label="__t('cancel', 'administration')"
        action="delete"
        loading-target="openDeleteModal, delete" />
</div>
