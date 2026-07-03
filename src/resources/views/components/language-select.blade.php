@props(['name' => 'locale', 'display' => 'hidden sm:block'])

<div
    class="{{ $display }}"
    x-data="{
        locale: localStorage.getItem('velntra.locale') || 'en',
        changeLocale() {
            localStorage.setItem('velntra.locale', this.locale);
            window.dispatchEvent(new CustomEvent('toast', {
                detail: {
                    type: 'info',
                    title: 'Language updated',
                    message: this.locale === 'es' ? 'Español selected.' : 'English selected.',
                },
            }));
        },
    }"
>
    <label for="{{ $name }}" class="sr-only">Language</label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        x-model="locale"
        x-on:change="changeLocale()"
        {{ $attributes->merge(['class' => 'h-10 rounded-xl border-primary-200 bg-white py-0 pl-3 pr-8 text-sm font-medium text-primary-700 shadow-sm focus:border-accent-500 focus:ring-accent-500']) }}
    >
        <option value="en">English</option>
        <option value="es">Español</option>
    </select>
</div>
