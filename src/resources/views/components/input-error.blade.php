@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'space-y-1 text-sm text-red-600']) }}>
        @foreach ((array) $messages as $message)
            <li class="flex gap-2">
                <span class="mt-2 h-1 w-1 shrink-0 rounded-full bg-red-500"></span>
                <span>{{ $message }}</span>
            </li>
        @endforeach
    </ul>
@endif
