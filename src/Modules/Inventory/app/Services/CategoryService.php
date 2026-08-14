<?php

namespace Modules\Inventory\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Inventory\Models\Category;

class CategoryService
{
    /**
     * Find a category by its ID or fail.
     */
    public function find(int $id): Category
    {
        return Category::findOrFail($id);
    }

    /**
     * Create a new category.
     *
     * @param array{code: string, name: string, description?: ?string, is_active?: bool} $data
     */
    public function create(array $data): Category
    {
        return DB::transaction(function () use ($data) {
            return Category::create([
                'code'        => $data['code'],
                'name'        => $data['name'],
                'description' => $data['description'] ?? null,
                'is_active'   => $data['is_active'] ?? true,
                'created_by'  => Auth::id(),
            ]);
        });
    }

    /**
     * Update an existing category.
     *
     * @param array{code?: string, name?: string, description?: ?string, is_active?: bool} $data
     */
    public function update(Category $category, array $data): Category
    {
        return DB::transaction(function () use ($category, $data) {
            $category->update([
                'code'        => $data['code'] ?? $category->code,
                'name'        => $data['name'] ?? $category->name,
                'description' => array_key_exists('description', $data) ? $data['description'] : $category->description,
                'is_active'   => $data['is_active'] ?? $category->is_active,
                'updated_by'  => Auth::id(),
            ]);

            return $category->fresh();
        });
    }

    /**
     * Soft delete a category and record who deleted it.
     */
    public function delete(Category $category): bool
    {
        return DB::transaction(function () use ($category) {
            $category->update([
                'deleted_by' => Auth::id(),
            ]);

            return (bool) $category->delete();
        });
    }

    /**
     * Toggle active status of a category.
     */
    public function toggleStatus(Category $category): Category
    {
        return DB::transaction(function () use ($category) {
            $category->update([
                'is_active'  => ! $category->is_active,
                'updated_by' => Auth::id(),
            ]);

            return $category->fresh();
        });
    }
}
