<?php

namespace Modules\Reports\Livewire;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Reports\Exports\InventoryReportExport;
use Modules\Reports\Services\ReportService;
use Modules\Settings\Models\Setting;

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

    public function exportExcel(ReportService $reports): mixed
    {
        Gate::authorize('reports.view');

        return Excel::download(
            new InventoryReportExport($reports->inventoryQuery($this->search, $this->status)->get()),
            'reporte-inventario-'.now()->format('Y-m-d').'.xlsx',
        );
    }

    public function exportPdf(ReportService $reports): mixed
    {
        Gate::authorize('reports.view');

        $pdf = Pdf::loadView('reports::pdf.inventory', [
            'products' => $reports->inventoryQuery($this->search, $this->status)->get(),
            'summary' => $reports->inventorySummary($this->search, $this->status),
            'currency' => $reports->currency(),
            'settings' => Setting::getSettings(),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print $pdf->output(),
            'reporte-inventario-'.now()->format('Y-m-d').'.pdf',
            ['Content-Type' => 'application/pdf'],
        );
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
