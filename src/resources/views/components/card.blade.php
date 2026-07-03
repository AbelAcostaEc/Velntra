@props(['title' => null, 'description' => null])

<section {{ $attributes->merge(['class' => 'rounded-xl border border-primary-200 bg-white shadow-sm']) }}>
    @if ($title || $description || isset($header))
        <div class="border-b border-primary-200 px-5 py-4">
            @isset($header)
                {{ $header }}
            @else
                @if ($title)<h3 class="text-sm font-semibold text-primary-950">{{ $title }}</h3>@endif
                @if ($description)<p class="mt-1 text-sm text-primary-500">{{ $description }}</p>@endif
            @endisset
        </div>
    @endif

    <div class="p-5">{{ $slot }}</div>
</section>
