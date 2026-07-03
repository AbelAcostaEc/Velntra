@props(['type' => 'table', 'rows' => 5])

@if ($type === 'card')
    <div {{ $attributes->merge(['class' => 'animate-pulse rounded-xl border border-primary-200 bg-white p-5 shadow-sm']) }}>
        <div class="h-4 w-1/3 rounded bg-primary-100"></div>
        <div class="mt-4 h-8 w-1/2 rounded bg-primary-100"></div>
        <div class="mt-6 space-y-3">
            <div class="h-3 rounded bg-primary-100"></div>
            <div class="h-3 w-5/6 rounded bg-primary-100"></div>
        </div>
    </div>
@elseif ($type === 'form')
    <div {{ $attributes->merge(['class' => 'animate-pulse space-y-5 rounded-xl border border-primary-200 bg-white p-5 shadow-sm']) }}>
        @for ($index = 0; $index < $rows; $index++)
            <div>
                <div class="h-3 w-24 rounded bg-primary-100"></div>
                <div class="mt-2 h-10 rounded-xl bg-primary-100"></div>
            </div>
        @endfor
    </div>
@else
    <div {{ $attributes->merge(['class' => 'animate-pulse rounded-xl border border-primary-200 bg-white shadow-sm']) }}>
        <div class="border-b border-primary-200 p-4">
            <div class="h-10 max-w-sm rounded-xl bg-primary-100"></div>
        </div>
        <div class="divide-y divide-primary-100">
            @for ($index = 0; $index < $rows; $index++)
                <div class="grid grid-cols-4 gap-4 p-4">
                    <div class="h-4 rounded bg-primary-100"></div>
                    <div class="h-4 rounded bg-primary-100"></div>
                    <div class="h-4 rounded bg-primary-100"></div>
                    <div class="h-4 rounded bg-primary-100"></div>
                </div>
            @endfor
        </div>
    </div>
@endif
