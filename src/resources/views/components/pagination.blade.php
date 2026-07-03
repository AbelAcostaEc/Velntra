@props([
    'paginator' => null,
    'summary' => null,
    'currentPage' => 1,
    'lastPage' => 1,
    'from' => null,
    'to' => null,
    'total' => null,
    'livewire' => false,
    'pageName' => 'page',
])

@php
    if ($paginator) {
        $currentPage = method_exists($paginator, 'currentPage') ? $paginator->currentPage() : $currentPage;
        $lastPage = method_exists($paginator, 'lastPage') ? $paginator->lastPage() : $lastPage;
        $from = method_exists($paginator, 'firstItem') ? $paginator->firstItem() : $from;
        $to = method_exists($paginator, 'lastItem') ? $paginator->lastItem() : $to;
        $total = method_exists($paginator, 'total') ? $paginator->total() : $total;
    }

    $from = $from ?? (($total ?? 0) > 0 ? 1 : 0);
    $to = $to ?? min(10, $total ?? 0);
    $total = $total ?? 24;
    $summary = $summary ?? "Showing {$from} to {$to} of {$total} results";

    $start = max(1, $currentPage - 1);
    $end = min($lastPage, $currentPage + 1);

    if ($currentPage <= 2) {
        $end = min($lastPage, 3);
    }

    if ($currentPage >= $lastPage - 1) {
        $start = max(1, $lastPage - 2);
    }

    $pageUrl = fn ($page) => $paginator && method_exists($paginator, 'url') ? $paginator->url($page) : '#';
    $previousUrl = $paginator && method_exists($paginator, 'previousPageUrl') ? $paginator->previousPageUrl() : '#';
    $nextUrl = $paginator && method_exists($paginator, 'nextPageUrl') ? $paginator->nextPageUrl() : '#';
    $gotoPage = fn ($page) => $pageName === 'page' ? "gotoPage({$page})" : "gotoPage({$page}, '{$pageName}')";
    $previousPage = $pageName === 'page' ? 'previousPage' : "previousPage('{$pageName}')";
    $nextPage = $pageName === 'page' ? 'nextPage' : "nextPage('{$pageName}')";
@endphp

<nav {{ $attributes->merge(['class' => 'flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between']) }} aria-label="Pagination">
    <p class="text-sm text-primary-500">{{ $summary }}</p>

    <div class="flex items-center justify-between gap-2 sm:hidden">
        @if ($currentPage > 1)
            @if ($livewire)
                <button type="button" wire:click="{{ $previousPage }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-white px-3 text-sm font-medium text-primary-700 shadow-sm hover:bg-primary-50">Previous</button>
            @else
                <a href="{{ $previousUrl }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-white px-3 text-sm font-medium text-primary-700 shadow-sm hover:bg-primary-50">Previous</a>
            @endif
        @else
            <span class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-primary-50 px-3 text-sm font-medium text-primary-400">Previous</span>
        @endif

        <span class="text-sm font-medium text-primary-600">Page {{ $currentPage }} of {{ $lastPage }}</span>

        @if ($currentPage < $lastPage)
            @if ($livewire)
                <button type="button" wire:click="{{ $nextPage }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-white px-3 text-sm font-medium text-primary-700 shadow-sm hover:bg-primary-50">Next</button>
            @else
                <a href="{{ $nextUrl }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-white px-3 text-sm font-medium text-primary-700 shadow-sm hover:bg-primary-50">Next</a>
            @endif
        @else
            <span class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-primary-50 px-3 text-sm font-medium text-primary-400">Next</span>
        @endif
    </div>

    <div class="hidden items-center gap-1 sm:flex">
        @if ($currentPage > 1)
            @if ($livewire)
                <button type="button" wire:click="{{ $previousPage }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-white px-3 text-sm font-medium text-primary-700 shadow-sm hover:bg-primary-50">Previous</button>
            @else
                <a href="{{ $previousUrl }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-white px-3 text-sm font-medium text-primary-700 shadow-sm hover:bg-primary-50">Previous</a>
            @endif
        @else
            <span class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-primary-50 px-3 text-sm font-medium text-primary-400">Previous</span>
        @endif

        @if ($start > 1)
            @if ($livewire)
                <button type="button" wire:click="{{ $gotoPage(1) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-medium text-primary-600 hover:bg-primary-100">1</button>
            @else
                <a href="{{ $pageUrl(1) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-medium text-primary-600 hover:bg-primary-100">1</a>
            @endif
            @if ($start > 2)
                <span class="px-2 text-sm text-primary-400">...</span>
            @endif
        @endif

        @for ($page = $start; $page <= $end; $page++)
            @if ($page === $currentPage)
                <span aria-current="page" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl bg-primary-900 px-3 text-sm font-semibold text-white">{{ $page }}</span>
            @else
                @if ($livewire)
                    <button type="button" wire:click="{{ $gotoPage($page) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-medium text-primary-600 hover:bg-primary-100">{{ $page }}</button>
                @else
                    <a href="{{ $pageUrl($page) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-medium text-primary-600 hover:bg-primary-100">{{ $page }}</a>
                @endif
            @endif
        @endfor

        @if ($end < $lastPage)
            @if ($end < $lastPage - 1)
                <span class="px-2 text-sm text-primary-400">...</span>
            @endif
            @if ($livewire)
                <button type="button" wire:click="{{ $gotoPage($lastPage) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-medium text-primary-600 hover:bg-primary-100">{{ $lastPage }}</button>
            @else
                <a href="{{ $pageUrl($lastPage) }}" class="inline-flex h-9 min-w-9 items-center justify-center rounded-xl px-3 text-sm font-medium text-primary-600 hover:bg-primary-100">{{ $lastPage }}</a>
            @endif
        @endif

        @if ($currentPage < $lastPage)
            @if ($livewire)
                <button type="button" wire:click="{{ $nextPage }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-white px-3 text-sm font-medium text-primary-700 shadow-sm hover:bg-primary-50">Next</button>
            @else
                <a href="{{ $nextUrl }}" class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-white px-3 text-sm font-medium text-primary-700 shadow-sm hover:bg-primary-50">Next</a>
            @endif
        @else
            <span class="inline-flex h-9 items-center justify-center rounded-xl border border-primary-200 bg-primary-50 px-3 text-sm font-medium text-primary-400">Next</span>
        @endif
    </div>
</nav>
