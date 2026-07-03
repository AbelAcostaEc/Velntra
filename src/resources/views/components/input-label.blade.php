@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-primary-700']) }}>
    {{ $value ?? $slot }}
</label>
