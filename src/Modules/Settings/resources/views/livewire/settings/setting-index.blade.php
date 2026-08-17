<div>
    <x-crud-page :title="__t('settings_management', 'settings')" :description="__t('settings_description', 'settings')">
        <x-slot:actions>
            @can('update', \Modules\Settings\Models\Setting::class)
                <x-button
                    type="button"
                    wire:click="save"
                    wire:loading.attr="disabled">
                    <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                    </svg>
                    <span wire:loading.remove wire:target="save">{{ __t('save_changes', 'settings') }}</span>
                    <span wire:loading wire:target="save">{{ __t('saving', 'settings') }}</span>
                </x-button>
            @endcan
        </x-slot:actions>

        <x-slot:content>
            <form wire:submit.prevent="save" class="space-y-6">
                
                {{-- Sección 1: Información General de la Empresa --}}
                <div class="rounded-2xl border border-primary-200 bg-white/80 p-6 shadow-sm backdrop-blur-sm">
                    <div class="mb-5 border-b border-primary-100 pb-4">
                        <h2 class="text-base font-semibold text-primary-900">{{ __t('company_info_title', 'settings') }}</h2>
                        <p class="mt-1 text-xs text-primary-500">{{ __t('company_info_description', 'settings') }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <x-input
                                :label="__t('field_company_name', 'settings')"
                                name="company_name"
                                :placeholder="__t('field_company_name_placeholder', 'settings')"
                                wire:model="company_name"
                                required />
                        </div>

                        <div>
                            <x-input
                                :label="__t('field_ruc', 'settings')"
                                name="ruc"
                                :placeholder="__t('field_ruc_placeholder', 'settings')"
                                wire:model="ruc" />
                        </div>

                        <div>
                            <x-input
                                :label="__t('field_phone', 'settings')"
                                name="phone"
                                :placeholder="__t('field_phone_placeholder', 'settings')"
                                wire:model="phone" />
                        </div>

                        <div>
                            <x-input
                                :label="__t('field_email', 'settings')"
                                name="email"
                                type="email"
                                :placeholder="__t('field_email_placeholder', 'settings')"
                                wire:model="email" />
                        </div>

                        <div>
                            <x-input
                                :label="__t('field_address', 'settings')"
                                name="address"
                                :placeholder="__t('field_address_placeholder', 'settings')"
                                wire:model="address" />
                        </div>
                    </div>
                </div>

                {{-- Sección 2: Identidad Visual y Logotipo --}}
                <div class="rounded-2xl border border-primary-200 bg-white/80 p-6 shadow-sm backdrop-blur-sm">
                    <div class="mb-5 border-b border-primary-100 pb-4">
                        <h2 class="text-base font-semibold text-primary-900">{{ __t('branding_title', 'settings') }}</h2>
                        <p class="mt-1 text-xs text-primary-500">{{ __t('branding_description', 'settings') }}</p>
                    </div>

                    <div class="flex flex-col sm:flex-row items-start gap-6">
                        {{-- Vista previa del logotipo actual o nuevo --}}
                        <div class="flex flex-col items-center">
                            <div class="relative flex h-32 w-32 items-center justify-center overflow-hidden rounded-2xl border-2 border-dashed border-primary-300 bg-primary-50 p-2 shadow-inner">
                                @if ($logoFile)
                                    <img src="{{ $logoFile->temporaryUrl() }}" alt="Preview" class="h-full w-full object-contain" />
                                @elseif ($logo)
                                    <img src="{{ str_starts_with($logo, 'http') ? $logo : asset('storage/' . $logo) }}" alt="Logo" class="h-full w-full object-contain" />
                                @else
                                    <div class="flex flex-col items-center justify-center text-primary-400">
                                        <svg class="h-10 w-10 stroke-1" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                        </svg>
                                        <span class="mt-1 text-[10px] uppercase font-semibold tracking-wider text-primary-400">{{ __t('no_logo', 'settings') }}</span>
                                    </div>
                                @endif

                                {{-- Indicador de carga al subir archivo --}}
                                <div wire:loading wire:target="logoFile" class="absolute inset-0 flex items-center justify-center bg-white/80 backdrop-blur-xs">
                                    <svg class="h-6 w-6 animate-spin text-brand-600" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        {{-- Controles de carga y ayuda --}}
                        <div class="flex-1 space-y-3">
                            <label class="block text-sm font-medium text-primary-700">
                                {{ __t('field_logo', 'settings') }}
                            </label>
                            <p class="text-xs text-primary-500">
                                {{ __t('field_logo_help', 'settings') }}
                            </p>

                            <div class="flex flex-wrap items-center gap-3 pt-1">
                                <label class="inline-flex cursor-pointer items-center justify-center gap-2 rounded-xl border border-primary-200 bg-white px-4 py-2 text-xs font-semibold text-primary-700 shadow-xs transition hover:bg-primary-50 hover:border-primary-300">
                                    <svg class="h-4 w-4 text-primary-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
                                    </svg>
                                    <span>{{ $logo || $logoFile ? __t('change_logo', 'settings') : __t('select_logo', 'settings') }}</span>
                                    <input type="file" wire:model="logoFile" accept="image/png,image/jpeg,image/webp" class="hidden" />
                                </label>

                                @if ($logo || $logoFile)
                                    <button
                                        type="button"
                                        wire:click="removeLogo"
                                        class="inline-flex cursor-pointer items-center justify-center gap-1.5 rounded-xl border border-rose-200 bg-rose-50/60 px-3 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                        </svg>
                                        {{ __t('remove_logo', 'settings') }}
                                    </button>
                                @endif
                            </div>

                            @error('logoFile')
                                <p class="text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Sección 3: Moneda e Impuestos --}}
                <div class="rounded-2xl border border-primary-200 bg-white/80 p-6 shadow-sm backdrop-blur-sm">
                    <div class="mb-5 border-b border-primary-100 pb-4">
                        <h2 class="text-base font-semibold text-primary-900">{{ __t('financial_title', 'settings') }}</h2>
                        <p class="mt-1 text-xs text-primary-500">{{ __t('financial_description', 'settings') }}</p>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        {{-- Selector de Moneda --}}
                        <div>
                            <label class="block text-sm font-medium text-primary-700 mb-1.5">
                                {{ __t('field_currency', 'settings') }}
                                <span class="text-rose-500">*</span>
                            </label>

                            <select
                                wire:model.live="currency_id"
                                class="w-full rounded-xl border border-primary-200 bg-white px-3.5 py-2.5 text-sm text-primary-900 shadow-xs focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500/20">
                                <option value="">{{ __t('field_currency_placeholder', 'settings') }}</option>
                                @foreach ($currencies as $curr)
                                    <option value="{{ $curr->id }}">
                                        {{ $curr->code }} ({{ $curr->symbol }}) — {{ $curr->name }}
                                    </option>
                                @endforeach
                            </select>

                            @error('currency_id')
                                <p class="mt-1 text-xs text-rose-600 font-medium">{{ $message }}</p>
                            @enderror

                            @if ($selectedCurrency)
                                <div class="mt-2.5 inline-flex items-center gap-2 rounded-lg bg-brand-50 px-2.5 py-1 text-xs text-brand-800 font-medium">
                                    <span class="font-mono font-bold">{{ $selectedCurrency->symbol }}</span>
                                    <span>{{ $selectedCurrency->name }} ({{ $selectedCurrency->code }})</span>
                                </div>
                            @endif
                        </div>

                        {{-- Porcentaje de Impuesto --}}
                        <div>
                            <x-input
                                :label="__t('field_tax_percentage', 'settings')"
                                name="tax_percentage"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                :placeholder="__t('field_tax_percentage_placeholder', 'settings')"
                                wire:model="tax_percentage"
                                required />

                            <p class="mt-1 text-xs text-primary-400">
                                {{ __t('field_tax_percentage_help', 'settings') }}
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Botones de acción inferiores --}}
                @can('update', \Modules\Settings\Models\Setting::class)
                    <div class="flex justify-end pt-2">
                        <x-button
                            type="submit"
                            wire:loading.attr="disabled">
                            <svg class="h-4 w-4 mr-1.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                            </svg>
                            <span wire:loading.remove wire:target="save">{{ __t('save_changes', 'settings') }}</span>
                            <span wire:loading wire:target="save">{{ __t('saving', 'settings') }}</span>
                        </x-button>
                    </div>
                @endcan
            </form>
        </x-slot:content>
    </x-crud-page>
</div>
