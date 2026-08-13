<div>
    <x-crud-page :title="__t('roles_management', 'administration')" :description="__t('roles_management_description', 'administration')">
        <x-slot:actions>
            @can('create', \Spatie\Permission\Models\Role::class)
                <x-button
                    wire:click="openCreateModal"
                    x-on:click="$dispatch('open-modal', 'role-form')"
                    wire:loading.attr="disabled">
                    <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ __t('create_role', 'administration') }}
                </x-button>
            @endcan
        </x-slot:actions>

        <x-slot:summary>
            <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                <x-stat-card :label="__t('total_roles', 'administration')" value="{{ $totalRoles }}" :trend="__t('configured_roles', 'administration')" variant="info" />
                <x-stat-card :label="__t('total_permissions', 'administration')" value="{{ $totalPermissions }}" :trend="__t('available_permissions', 'administration')" variant="success" />
                <x-stat-card :label="__t('assigned_users', 'administration')" value="{{ $assignedUsersCount }}" :trend="__t('active_assignments', 'administration')" variant="warning" />
            </div>
        </x-slot:summary>

        <x-slot:content>
            <x-table loading-target="search">
                <x-slot:toolbar>
                    <x-table-search :placeholder="__t('search_roles_placeholder', 'administration')" wire:model.live.debounce.300ms="search" />
                </x-slot:toolbar>

                <x-slot:head>
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_name', 'administration') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_permissions', 'administration') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_users', 'administration') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-primary-500">{{ __t('col_actions', 'administration') }}</th>
                    </tr>
                </x-slot:head>

                @forelse($roles as $role)
                    <tr class="hover:bg-primary-50/50 transition-colors">
                        <td class="px-4 py-4 text-sm font-medium text-primary-950">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold">{{ ucfirst($role->name) }}</span>
                                @if($role->name === 'admin')
                                    <x-badge variant="info">{{ __t('full_access', 'administration') }}</x-badge>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-primary-600">
                            <div class="flex items-center gap-1.5 flex-wrap max-w-md">
                                <x-badge variant="{{ $role->permissions_count > 0 ? 'success' : 'neutral' }}">
                                    {{ $role->permissions_count }} {{ __t('permissions_count', 'administration') }}
                                </x-badge>
                                @if($role->permissions_count === $totalPermissions && $totalPermissions > 0)
                                    <span class="text-xs text-emerald-600 font-medium">({{ __t('all_permissions', 'administration') }})</span>
                                @endif
                            </div>
                        </td>
                        <td class="px-4 py-4 text-sm text-primary-600">
                            <x-badge variant="neutral">
                                {{ $role->users_count }} {{ __t('users_count', 'administration') }}
                            </x-badge>
                        </td>
                        <td class="px-4 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                @can('update', $role)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $role->id }})"
                                        x-on:click="$dispatch('open-modal', 'role-form')">
                                        {{ __t('edit', 'administration') }}
                                    </x-button>
                                @endcan
                                @can('delete', $role)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $role->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-role')">
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
                                :title="__t('no_roles_title', 'administration')"
                                :description="__t('no_roles_description', 'administration')" />
                        </td>
                    </tr>
                @endforelse

                <x-slot:mobile>
                    @foreach ($roles as $role)
                        <x-mobile-record-card
                            :title="ucfirst($role->name)">
                            <x-slot:status>
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    <x-badge variant="{{ $role->permissions_count > 0 ? 'success' : 'neutral' }}">
                                        {{ $role->permissions_count }} {{ __t('permissions_count', 'administration') }}
                                    </x-badge>
                                    <x-badge variant="neutral">
                                        {{ $role->users_count }} {{ __t('users_count', 'administration') }}
                                    </x-badge>
                                </div>
                            </x-slot:status>

                            <x-slot:actions>
                                @can('update', $role)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        wire:click="openEditModal({{ $role->id }})"
                                        x-on:click="$dispatch('open-modal', 'role-form')">
                                        {{ __t('edit', 'administration') }}
                                    </x-button>
                                @endcan
                                @can('delete', $role)
                                    <x-button
                                        variant="danger"
                                        size="sm"
                                        wire:click="openDeleteModal({{ $role->id }})"
                                        x-on:click="$dispatch('open-modal', 'delete-role')">
                                        {{ __t('delete', 'administration') }}
                                    </x-button>
                                @endcan
                            </x-slot:actions>
                        </x-mobile-record-card>
                    @endforeach
                </x-slot:mobile>

                <x-slot:pagination>
                    <div class="px-4 py-3">
                        {{ $roles->links() }}
                    </div>
                </x-slot:pagination>
            </x-table>
        </x-slot:content>
    </x-crud-page>

    {{-- Modal Formulario Crear / Editar --}}
    <x-modal
        name="role-form"
        :title="$selectedRoleId ? __t('edit_role', 'administration') : __t('create_role', 'administration')"
        :description="__t('role_form_description', 'administration')"
        loading-target="openCreateModal, openEditModal">
        <form wire:submit.prevent="save" class="space-y-6 p-6">
            <x-input
                :label="__t('field_role_name', 'administration')"
                name="name"
                :placeholder="__t('field_role_name_placeholder', 'administration')"
                wire:model="name"
                required />

            {{-- Permisos agrupados por módulo --}}
            <div class="space-y-4">
                <div class="flex items-center justify-between border-b border-primary-200 pb-2">
                    <div>
                        <label class="text-sm font-semibold text-primary-900">
                            {{ __t('field_permissions', 'administration') }}
                        </label>
                        <p class="text-xs text-primary-500">
                            {{ count($selectedPermissions) }} {{ __t('permissions_selected', 'administration') }}
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            wire:click="selectAllPermissions"
                            class="text-xs font-medium text-primary-600 hover:text-primary-900 hover:underline">
                            {{ __t('select_all', 'administration') }}
                        </button>
                        <span class="text-primary-300">|</span>
                        <button
                            type="button"
                            wire:click="deselectAllPermissions"
                            class="text-xs font-medium text-primary-600 hover:text-primary-900 hover:underline">
                            {{ __t('deselect_all', 'administration') }}
                        </button>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 max-h-96 overflow-y-auto pr-1">
                    @foreach ($permissionsByModule as $moduleName => $permissions)
                        @php
                            $modulePermNames = $permissions->pluck('name')->toArray();
                            $allSelected = count(array_intersect($modulePermNames, $selectedPermissions)) === count($modulePermNames);
                        @endphp
                        <div class="rounded-xl border border-primary-200 bg-primary-50/50 p-4 transition-colors hover:border-primary-300">
                            <div class="flex items-center justify-between mb-3 border-b border-primary-200/60 pb-2">
                                <span class="text-xs font-bold uppercase tracking-wider text-primary-700">
                                    {{ __t('module_' . $moduleName, 'administration', 'layout') !== 'administration::layout.module_' . $moduleName ? __t('module_' . $moduleName, 'administration') : ucfirst($moduleName) }}
                                </span>
                                <button
                                    type="button"
                                    wire:click="toggleModulePermissions({{ json_encode($modulePermNames) }})"
                                    class="text-xs font-medium text-primary-500 hover:text-primary-800">
                                    {{ $allSelected ? __t('deselect_all', 'administration') : __t('select_all', 'administration') }}
                                </button>
                            </div>
                            <div class="space-y-2">
                                @foreach ($permissions as $perm)
                                    <label class="flex items-center gap-2.5 cursor-pointer select-none group">
                                        <input
                                            type="checkbox"
                                            value="{{ $perm->name }}"
                                            wire:model="selectedPermissions"
                                            class="h-4 w-4 rounded border-primary-300 text-primary-600 focus:ring-primary-500 cursor-pointer"
                                        />
                                        <span class="text-xs font-mono text-primary-700 group-hover:text-primary-900 transition-colors">
                                            {{ $perm->name }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                @error('selectedPermissions')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('selectedPermissions.*')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex justify-end gap-2 border-t border-primary-200 pt-5">
                <x-button
                    type="button"
                    variant="secondary"
                    x-on:click="$dispatch('close-modal', 'role-form')">
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
        name="delete-role"
        :title="__t('delete_role_title', 'administration')"
        :description="__t('delete_role_description', 'administration')"
        :confirm-label="__t('delete', 'administration')"
        :cancel-label="__t('cancel', 'administration')"
        action="delete"
        loading-target="openDeleteModal, delete" />
</div>
