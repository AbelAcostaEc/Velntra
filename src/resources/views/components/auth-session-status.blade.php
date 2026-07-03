@props(['status'])

@if ($status)
    <div {{ $attributes->merge(['class' => 'rounded-xl border border-accent-200 bg-accent-50 px-4 py-3 text-sm font-medium text-accent-700']) }}>
        {{ $status }}
    </div>
@endif
