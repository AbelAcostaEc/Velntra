@props(['label' => null, 'name' => null, 'id' => null, 'error' => null])

@php
    $fieldId = $id ?? $name ?? 'checkbox-'.uniqid();
    $message = $error ?: ($name ? $errors->first($name) : null);
@endphp

<div>
    <label for="{{ $fieldId }}" class="inline-flex items-start gap-3 text-sm text-primary-700">
        <input
            id="{{ $fieldId }}"
            name="{{ $name }}"
            type="checkbox"
            {{ $attributes->merge(['class' => 'mt-0.5 rounded border-primary-300 text-accent-600 shadow-sm focus:ring-accent-500 disabled:cursor-not-allowed disabled:opacity-50']) }}
        >
        @if ($label)
            <span>{{ $label }}</span>
        @else
            <span>{{ $slot }}</span>
        @endif
    </label>

    @if ($message)
        <p class="mt-1.5 text-sm text-red-600">{{ $message }}</p>
    @endif
</div>
