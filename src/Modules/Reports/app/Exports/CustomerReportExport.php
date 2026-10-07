<?php

namespace Modules\Reports\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Customers\Models\Customer;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class CustomerReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    private int $position = 0;

    /** @param Collection<int, Customer> $customers */
    public function __construct(private readonly Collection $customers) {}

    public function collection(): Collection
    {
        return $this->customers;
    }

    /** @return list<string> */
    public function headings(): array
    {
        return ['Posición', 'Cliente', 'Documento', 'Correo', 'Teléfono', 'Cantidad de compras', 'Total comprado'];
    }

    /** @return list<int|float|string> */
    public function map($customer): array
    {
        return [
            ++$this->position,
            $customer->name,
            $customer->document,
            $customer->email ?? '',
            $customer->phone ?? '',
            $customer->purchases_count,
            (float) $customer->purchases_total,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('G:G')->getNumberFormat()->setFormatCode('#,##0.00');

        return [1 => ['font' => ['bold' => true]]];
    }
}
