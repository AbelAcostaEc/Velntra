<?php

namespace Modules\Reports\Livewire;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Reports\Exports\CustomerReportExport;
use Modules\Reports\Services\ReportService;
use Modules\Settings\Models\Setting;

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
        $this->validate($this->dateRules());
        $this->resetPage();
    }

    public function updated(string $property): void
    {
        if (in_array($property, ['search', 'perPage'], true)) {
            $this->perPage = in_array($this->perPage, [10, 25, 50], true) ? $this->perPage : 10;
            $this->resetPage();
        }
    }

    public function exportExcel(ReportService $reports): mixed
    {
        Gate::authorize('reports.view');
        $period = $this->exportPeriod($reports);

        return Excel::download(
            new CustomerReportExport($reports->customersQuery($this->dateFrom, $this->dateTo, $this->search)->get()),
            "reporte-clientes-{$period}.xlsx",
        );
    }

    public function exportPdf(ReportService $reports): mixed
    {
        Gate::authorize('reports.view');
        $period = $this->exportPeriod($reports);

        $pdf = Pdf::loadView('reports::pdf.customers', [
            'customers' => $reports->customersQuery($this->dateFrom, $this->dateTo, $this->search)->get(),
            'summary' => $reports->customersSummary($this->dateFrom, $this->dateTo, $this->search),
            'currency' => $reports->currency(),
            'settings' => Setting::getSettings(),
            'dateFrom' => $this->dateFrom,
            'dateTo' => $this->dateTo,
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print $pdf->output(),
            "reporte-clientes-{$period}.pdf",
            ['Content-Type' => 'application/pdf'],
        );
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

    /** @return array<string, list<string>> */
    private function dateRules(): array
    {
        return [
            'dateFrom' => ['required', 'date'],
            'dateTo' => ['required', 'date', 'after_or_equal:dateFrom'],
        ];
    }

    private function exportPeriod(ReportService $reports): string
    {
        return $reports->hasValidDateRange($this->dateFrom, $this->dateTo)
            ? "{$this->dateFrom}-{$this->dateTo}"
            : 'sin-datos';
    }
}
