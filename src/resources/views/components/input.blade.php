@props(['label' => null, 'name' => null, 'id' => null, 'type' => 'text', 'error' => null, 'required' => false])

@php
    $fieldId = $id ?? $name ?? 'input-'.uniqid();
    $message = $error ?: ($name ? $errors->first($name) : null);
@endphp

<div>
    @if ($label)
        <label for="{{ $fieldId }}" class="mb-1.5 block text-sm font-medium text-primary-700">
            {{ $label }} @if ($required)<span class="text-red-500">*</span>@endif
        </label>
    @endif

    <input
        id="{{ $fieldId }}"
        name="{{ $name }}"
        type="{{ $type }}"
        @required($required)
        {{ $attributes->merge(['class' => 'block w-full rounded-xl border-primary-200 bg-white text-sm text-primary-900 shadow-sm placeholder:text-primary-400 focus:border-accent-500 focus:ring-accent-500 disabled:cursor-not-allowed disabled:bg-primary-100 disabled:text-primary-500 '.($message ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : '')]) }}
    >

    @if ($message)
        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @endif
</div>
