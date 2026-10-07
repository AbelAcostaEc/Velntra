<?php

namespace Modules\Reports\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Modules\Inventory\Models\Product;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryReportExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    /** @param Collection<int, Product> $products */
    public function __construct(private readonly Collection $products) {}

    public function collection(): Collection
    {
        return $this->products;
    }

    /** @return list<string> */
    public function headings(): array
    {
        return ['SKU', 'Producto', 'Categorías', 'Stock', 'Stock mínimo', 'Costo unitario', 'Precio', 'Valor al costo', 'Valor de venta', 'Estado'];
    }

    /** @return list<int|float|string> */
    public function map($product): array
    {
        return [
            $product->sku,
            $product->name,
            $product->categories->pluck('name')->join(', '),
            $product->stock,
            $product->minimum_stock,
            (float) $product->cost,
            (float) $product->price,
            $product->stock * (float) $product->cost,
            $product->stock * (float) $product->price,
            $product->is_active ? 'Activo' : 'Inactivo',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        $sheet->getStyle('F:I')->getNumberFormat()->setFormatCode('#,##0.00');

        return [1 => ['font' => ['bold' => true]]];
    }
}
