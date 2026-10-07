<?php

namespace Modules\Reports\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Modules\Reports\Services\ReportService;

class InventoryReport extends Component
{
    use WithPagination;

    public string $search = '';

    public string $status = 'all';

    public int $perPage = 10;

    public function mount(): void
    {
        Gate::authorize('reports.view');
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'status', 'perPage'], true)) {
            $this->status = in_array($this->status, ['all', 'active', 'inactive', 'low_stock'], true) ? $this->status : 'all';
            $this->perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;
            $this->resetPage();
        }
    }

    public function render(ReportService $reports): View
    {
        Gate::authorize('reports.view');

        return view('reports::livewire.inventory-report', [
            'products' => $reports->inventoryQuery($this->search, $this->status)->paginate($this->perPage),
            'summary' => $reports->inventorySummary($this->search, $this->status),
            'currency' => $reports->currency(),
        ]);
    }
}
