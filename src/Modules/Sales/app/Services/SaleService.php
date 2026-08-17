<?php

namespace Modules\Sales\Services;

// Framework & Database
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

// Models
use Modules\Inventory\Models\Product;
use Modules\Sales\Models\Sale;
use Modules\Sales\Models\SaleItem;
use Modules\Settings\Models\Setting;

class SaleService
{
    /**
     * Buscar una venta por su ID o lanzar excepción.
     *
     * @param int $id
     * @return Sale
     */
    public function find(int $id): Sale
    {
        return Sale::with(['items.product', 'customer', 'user'])->findOrFail($id);
    }

    /**
     * Obtener todas las ventas pausadas / en espera (pendientes).
     *
     * @return Collection<int, Sale>
     */
    public function getPendingSales(): Collection
    {
        return Sale::with(['items.product', 'customer', 'user'])
            ->pending()
            ->latest('id')
            ->get();
    }

    /**
     * Crear una venta (completada o en espera) dentro de una transacción.
     *
     * @param array{
     *     customer_id: int,
     *     user_id: int,
     *     discount?: float|numeric|string,
     *     payment_method?: string,
     *     amount_paid?: float|numeric|string|null,
     *     change?: float|numeric|string,
     *     status?: string,
     *     notes?: string|null
     * } $data
     * @param array<int, array{
     *     product_id: int,
     *     quantity: int,
     *     price: float|numeric|string,
     *     cost?: float|numeric|string
     * }> $items
     * @return Sale
     * @throws InvalidArgumentException
     */
    public function createSale(array $data, array $items): Sale
    {
        if (empty($items)) {
            throw new InvalidArgumentException('Toda venta debe contener al menos un producto.');
        }

        return DB::transaction(function () use ($data, $items) {
            $isCompleted = ($data['status'] ?? 'completed') === 'completed';

            // 1. Validar existencias y preparar líneas de venta
            $calculatedSubtotal = 0.00;
            $itemsData = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);
                $quantity = (int) $item['quantity'];

                if ($quantity <= 0) {
                    throw new InvalidArgumentException("La cantidad para {$product->name} debe ser mayor a cero.");
                }

                if ($isCompleted && $product->stock < $quantity) {
                    throw new InvalidArgumentException("Stock insuficiente para el producto: {$product->name}. Disponible: {$product->stock}, Solicitado: {$quantity}");
                }

                $price = (float) ($item['price'] ?? $product->price);
                $cost = (float) ($item['cost'] ?? $product->cost);
                $lineSubtotal = round($quantity * $price, 2);
                $calculatedSubtotal += $lineSubtotal;

                $itemsData[] = [
                    'product'  => $product,
                    'quantity' => $quantity,
                    'price'    => $price,
                    'cost'     => $cost,
                    'subtotal' => $lineSubtotal,
                ];
            }

            // 2. Calcular impuestos y total
            $settings = Setting::getSettings();
            $taxRate = (float) ($settings->tax_percentage ?? 15.00);
            $discount = (float) ($data['discount'] ?? 0.00);
            $taxableBase = max(0.00, $calculatedSubtotal - $discount);
            $taxAmount = round($taxableBase * ($taxRate / 100), 2);
            $total = round($taxableBase + $taxAmount, 2);

            $amountPaid = isset($data['amount_paid']) && $data['amount_paid'] !== ''
                ? (float) $data['amount_paid']
                : ($isCompleted ? $total : null);

            $change = isset($data['change'])
                ? (float) $data['change']
                : max(0.00, ($amountPaid ? $amountPaid - $total : 0.00));

            // 3. Crear cabecera de venta
            $sale = Sale::create([
                'number'         => Sale::generateNextNumber(),
                'customer_id'    => $data['customer_id'],
                'user_id'        => $data['user_id'],
                'subtotal'       => $calculatedSubtotal,
                'discount'       => $discount,
                'tax'            => $taxAmount,
                'tax_percentage' => $taxRate,
                'total'          => $total,
                'payment_method' => $data['payment_method'] ?? 'cash',
                'amount_paid'    => $amountPaid,
                'change'         => $change,
                'status'         => $data['status'] ?? 'completed',
                'notes'          => $data['notes'] ?? null,
            ]);

            // 4. Guardar items y descontar stock si es completada
            foreach ($itemsData as $row) {
                SaleItem::create([
                    'sale_id'    => $sale->id,
                    'product_id' => $row['product']->id,
                    'quantity'   => $row['quantity'],
                    'price'      => $row['price'],
                    'cost'       => $row['cost'],
                    'subtotal'   => $row['subtotal'],
                ]);

                if ($isCompleted) {
                    $row['product']->decrement('stock', $row['quantity']);
                }
            }

            return $sale->load(['items.product', 'customer', 'user']);
        });
    }

    /**
     * Completar y cobrar una venta que estaba en espera (pending).
     *
     * @param Sale $sale
     * @param array{
     *     payment_method: string,
     *     amount_paid: float|numeric|string,
     *     change?: float|numeric|string,
     *     notes?: string|null
     * } $paymentData
     * @return Sale
     */
    public function completePendingSale(Sale $sale, array $paymentData): Sale
    {
        if ($sale->status !== 'pending') {
            throw new InvalidArgumentException('Solo se pueden cobrar ventas que estén en estado pendiente.');
        }

        return DB::transaction(function () use ($sale, $paymentData) {
            // Validar stock de cada item
            foreach ($sale->items as $item) {
                $product = $item->product;
                if ($product->stock < $item->quantity) {
                    throw new InvalidArgumentException("Stock insuficiente para: {$product->name}. Disponible: {$product->stock}");
                }
            }

            // Descontar inventario
            foreach ($sale->items as $item) {
                $item->product->decrement('stock', $item->quantity);
            }

            $amountPaid = (float) ($paymentData['amount_paid'] ?? $sale->total);
            $change = isset($paymentData['change'])
                ? (float) $paymentData['change']
                : max(0.00, $amountPaid - (float) $sale->total);

            $sale->update([
                'payment_method' => $paymentData['payment_method'] ?? 'cash',
                'amount_paid'    => $amountPaid,
                'change'         => $change,
                'status'         => 'completed',
                'notes'          => $paymentData['notes'] ?? $sale->notes,
            ]);

            return $sale->fresh(['items.product', 'customer', 'user']);
        });
    }

    /**
     * Anular una venta completada y restaurar el stock automáticamente (BR-034 / BR-035).
     *
     * @param Sale $sale
     * @param string|null $reason
     * @return Sale
     */
    public function cancelSale(Sale $sale, ?string $reason = null): Sale
    {
        if ($sale->status === 'cancelled') {
            throw new InvalidArgumentException('La venta ya se encuentra anulada.');
        }

        return DB::transaction(function () use ($sale, $reason) {
            // Restaurar stock solo si la venta había sido completada
            if ($sale->status === 'completed') {
                foreach ($sale->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                }
            }

            $sale->update([
                'status' => 'cancelled',
                'notes'  => $reason ? trim(($sale->notes ? $sale->notes . " | Anulación: " : "Anulación: ") . $reason) : $sale->notes,
            ]);

            return $sale->fresh(['items.product', 'customer', 'user']);
        });
    }

    /**
     * Eliminar definitivamente una venta pausada / en espera.
     *
     * @param Sale $sale
     * @return bool
     */
    public function deletePendingSale(Sale $sale): bool
    {
        if ($sale->status !== 'pending') {
            throw new InvalidArgumentException('Solo se pueden eliminar ventas que estén en estado pendiente.');
        }

        return DB::transaction(function () use ($sale) {
            $sale->items()->delete();
            return (bool) $sale->forceDelete();
        });
    }
}
