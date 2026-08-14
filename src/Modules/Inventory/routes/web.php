<?php

use Illuminate\Support\Facades\Route;
use Modules\Inventory\Http\Controllers\InventoryController;
use Modules\Inventory\Livewire\Categories\CategoryIndex;
use Modules\Inventory\Livewire\Products\ProductIndex;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('categories', CategoryIndex::class)->name('categories.index');
    Route::get('products', ProductIndex::class)->name('products.index');
    Route::resource('inventories', InventoryController::class)->names('inventory');
});
