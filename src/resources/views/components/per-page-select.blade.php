@props([
    'options' => [5, 10, 20, 30],
    'label' => null,
])

<div class="flex items-center gap-1.5">
    @if ($label)
        <span class="text-xs font-medium text-primary-500 whitespace-nowrap">{{ $label }}</span>
    @endif
    <select
        {{ $attributes->merge(['class' => 'h-10 rounded-xl border-primary-200 bg-white py-0 pl-3 pr-8 text-sm font-medium text-primary-700 shadow-sm focus:border-accent-500 focus:ring-accent-500']) }}>
        @foreach ($options as $option)
            <option value="{{ $option }}">{{ $option }} Items</option>
        @endforeach
    </select>
</div>
