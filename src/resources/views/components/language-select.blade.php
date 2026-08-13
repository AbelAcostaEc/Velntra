@props(['name' => 'locale', 'display' => 'hidden sm:block'])

@php
    $currentLocale = app()->getLocale();
@endphp

<div class="{{ $display }}">
    <label for="{{ $name }}" class="sr-only">Language</label>
    <select
        id="{{ $name }}"
        name="{{ $name }}"
        onchange="if (this.value) { window.location.href = '{{ url('locale') }}/' + this.value; }"
        {{ $attributes->merge(['class' => 'h-10 rounded-xl border-primary-200 bg-white py-0 pl-3 pr-8 text-sm font-medium text-primary-700 shadow-sm focus:border-accent-500 focus:ring-accent-500 cursor-pointer']) }}
    >
        <option value="en" {{ $currentLocale === 'en' ? 'selected' : '' }}>English</option>
        <option value="es" {{ $currentLocale === 'es' ? 'selected' : '' }}>Español</option>
    </select>
</div>
