<?php

namespace Modules\Reports\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Sales\Models\Sale;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SalesReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /** @param Collection<int, Sale> $sales */
    public function __construct(private readonly Collection $sales) {}

    public function collection(): Collection
    {
        return $this->sales;
    }

    /** @return list<string> */
    public function headings(): array
    {
        return ['Venta', 'Fecha', 'Cliente', 'Documento', 'Método de pago', 'Subtotal', 'Descuento', 'IVA', 'Total'];
    }

    /** @return list<int|float|string> */
    public function map($sale): array
    {
        return [
            $sale->number,
            $sale->created_at->format('Y-m-d H:i'),
            $sale->customer?->name ?? '',
            $sale->customer?->document ?? '',
            $sale->payment_method,
            (float) $sale->subtotal,
            (float) $sale->discount,
            (float) $sale->tax,
            (float) $sale->total,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('F:I')->getNumberFormat()->setFormatCode('#,##0.00');

        return [1 => ['font' => ['bold' => true]]];
    }
}
