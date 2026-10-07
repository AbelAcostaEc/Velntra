<?php

namespace Modules\Reports\Livewire;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Reports\Exports\SalesReportExport;
use Modules\Reports\Services\ReportService;
use Modules\Settings\Models\Setting;

class SalesReport extends Component
{
    use WithPagination;

    public string $dateFrom = '';

    public string $dateTo = '';

    public int $perPage = 10;

    public function mount(): void
    {
        Gate::authorize('reports.view');
        $this->dateFrom = now()->startOfMonth()->toDateString();
        $this->dateTo = now()->toDateString();
    }

    public function applyFilters(): void
    {
        $this->validate($this->dateRules());
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;
        $this->resetPage();
    }

    public function exportExcel(ReportService $reports): mixed
    {
        Gate::authorize('reports.view');
        $this->validate($this->dateRules());

        return Excel::download(
            new SalesReportExport($reports->salesQuery($this->dateFrom, $this->dateTo)->get()),
            "reporte-ventas-{$this->dateFrom}-{$this->dateTo}.xlsx",
        );
    }

    public function exportPdf(ReportService $reports): mixed
    {
        Gate::authorize('reports.view');
        $this->validate($this->dateRules());

        $pdf = Pdf::loadView('reports::pdf.sales', [
            'sales' => $reports->salesQuery($this->dateFrom, $this->dateTo)->get(),
            'summary' => $reports->salesSummary($this->dateFrom, $this->dateTo),
            'currency' => $reports->currency(),
            'settings' => Setting::getSettings(),
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print $pdf->output(),
            "reporte-ventas-{$this->dateFrom}-{$this->dateTo}.pdf",
            ['Content-Type' => 'application/pdf'],
        );
    }

    public function render(ReportService $reports): View
    {
        Gate::authorize('reports.view');

        return view('reports::livewire.sales-report', [
            'sales' => $reports->salesQuery($this->dateFrom, $this->dateTo)->paginate($this->perPage),
            'summary' => $reports->salesSummary($this->dateFrom, $this->dateTo),
            'currency' => $reports->currency(),
        ]);
    }

    /** @return array<string, list<string>> */
    private function dateRules(): array
    {
        return [
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
        ];
    }
}
