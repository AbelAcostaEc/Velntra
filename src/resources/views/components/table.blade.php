@props(['loadingTarget' => null])

<section {{ $attributes->merge(['class' => 'rounded-xl border border-primary-200 bg-white shadow-sm']) }}>
    @if (isset($toolbar) || isset($filters))
        <div class="space-y-4 border-b border-primary-200 p-4">
            @isset($toolbar)<div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">{{ $toolbar }}</div>@endisset
            @isset($filters)<div class="flex flex-wrap gap-3">{{ $filters }}</div>@endisset
        </div>
    @endif

    <div class="relative">
        <div
            @if ($loadingTarget) wire:loading.flex wire:target="{{ $loadingTarget }}" @else wire:loading.flex @endif
            class="absolute inset-0 z-10 hidden items-center justify-center bg-white/70 backdrop-blur-sm"
        >
            <div class="h-8 w-8 animate-spin rounded-full border-2 border-primary-200 border-t-accent-600"></div>
        </div>

        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full divide-y divide-primary-200">
                @isset($head)<thead class="bg-primary-50">{{ $head }}</thead>@endisset
                <tbody class="divide-y divide-primary-100 bg-white">{{ $slot }}</tbody>
            </table>
        </div>

        @isset($mobile)
            <div class="space-y-3 p-4 md:hidden">{{ $mobile }}</div>
        @endisset

        @isset($empty)
            {{ $empty }}
        @endisset
    </div>

    @isset($pagination)
        <div class="border-t border-primary-200 px-4 py-3">{{ $pagination }}</div>
    @endisset
</section>
