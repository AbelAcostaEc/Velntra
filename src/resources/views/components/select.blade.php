@props(['label' => null, 'name' => null, 'id' => null, 'error' => null, 'required' => false])

@php
    $fieldId = $id ?? $name ?? 'select-'.uniqid();
    $message = $error ?: ($name ? $errors->first($name) : null);
@endphp

<div>
    @if ($label)
        <label for="{{ $fieldId }}" class="mb-1.5 block text-sm font-medium text-primary-700">
            {{ $label }} @if ($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <select
        id="{{ $fieldId }}"
        name="{{ $name }}"
        @required($required)
        {{ $attributes->merge(['class' => 'block w-full rounded-xl border-primary-200 bg-white text-sm text-primary-900 shadow-sm focus:border-accent-500 focus:ring-accent-500 disabled:cursor-not-allowed disabled:bg-primary-100 disabled:text-primary-500 '.($message ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '')]) }}
    >
        {{ $slot }}
    </select>

    @if ($message)
        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @endif
</div>
