<?php

namespace Modules\Reports\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Reports\Services\ReportService;

class CustomerReport extends Component
{
    use WithPagination;

    public string $dateFrom = '';

    public string $dateTo = '';

    public string $search = '';

    public int $perPage = 10;

    public function mount(): void
    {
        Gate::authorize('reports.view');
        $this->dateFrom = now()->startOfYear()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function applyFilters(): void
    {
        $this->validate([
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
        ]);
        $this->resetPage();
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'perPage'], true)) {
            $this->perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;
            $this->resetPage();
        }
    }

    public function render(ReportService $reports): View
    {
        Gate::authorize('reports.view');

        return view('reports::livewire.customer-report', [
            'customers' => $reports->customersQuery($this->dateFrom, $this->dateTo, $this->search)->paginate($this->perPage),
            'summary' => $reports->customersSummary($this->dateFrom, $this->dateTo, $this->search),
            'currency' => $reports->currency(),
        ]);
    }
}
