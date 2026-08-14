<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Product;

class ProductService
{
    /**
     * Find a product by its ID or fail.
     */
    public function find(int $id): Product
    {
        return Product::with('categories')->findOrFail($id);
    }

    /**
     * Create a new product and sync its categories.
     *
     * @param array<string, mixed> $data
     * @param list<int> $categoryIds
     */
    public function create(array $data, array $categoryIds = []): Product
    {
        return DB::transaction(function () use ($data, $categoryIds) {
            $product = Product::create([
                'sku'           => $data['sku'],
                'barcode'       => $data['barcode'] ?? null,
                'type'          => $data['type'] ?? 'simple',
                'name'          => $data['name'],
                'description'   => $data['description'] ?? null,
                'cost'          => $data['cost'] ?? 0.00,
                'price'         => $data['price'] ?? 0.00,
                'stock'         => $data['stock'] ?? 0,
                'minimum_stock' => $data['minimum_stock'] ?? 0,
                'image'         => $data['image'] ?? null,
                'is_active'     => $data['is_active'] ?? true,
                'created_by'    => Auth::id(),
            ]);

            if (! empty($categoryIds)) {
                $product->categories()->sync($categoryIds);
            }

            return $product->fresh('categories');
        });
    }

    /**
     * Update an existing product and sync its categories.
     *
     * @param array<string, mixed> $data
     * @param list<int> $categoryIds
     */
    public function update(Product $product, array $data, array $categoryIds = []): Product
    {
        return DB::transaction(function () use ($product, $data, $categoryIds) {
            $product->update([
                'sku'           => $data['sku'] ?? $product->sku,
                'barcode'       => array_key_exists('barcode', $data) ? $data['barcode'] : $product->barcode,
                'type'          => $data['type'] ?? $product->type,
                'name'          => $data['name'] ?? $product->name,
                'description'   => array_key_exists('description', $data) ? $data['description'] : $product->description,
                'cost'          => array_key_exists('cost', $data) ? $data['cost'] : $product->cost,
                'price'         => array_key_exists('price', $data) ? $data['price'] : $product->price,
                'stock'         => array_key_exists('stock', $data) ? $data['stock'] : $product->stock,
                'minimum_stock' => array_key_exists('minimum_stock', $data) ? $data['minimum_stock'] : $product->minimum_stock,
                'image'         => array_key_exists('image', $data) ? $data['image'] : $product->image,
                'is_active'     => array_key_exists('is_active', $data) ? $data['is_active'] : $product->is_active,
                'updated_by'    => Auth::id(),
            ]);

            $product->categories()->sync($categoryIds);

            return $product->fresh('categories');
        });
    }

    /**
     * Soft delete a product and record who deleted it.
     */
    public function delete(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            $product->update([
                'deleted_by' => Auth::id(),
            ]);

            return (bool) $product->delete();
        });
    }

    /**
     * Toggle active status of a product.
     */
    public function toggleStatus(Product $product): Product
    {
        return DB::transaction(function () use ($product) {
            $product->update([
                'is_active'  => ! $product->is_active,
                'updated_by' => Auth::id(),
            ]);

            return $product->fresh('categories');
        });
    }
}
