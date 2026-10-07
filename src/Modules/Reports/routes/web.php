<?php

use Illuminate\Support\Facades\Route;
use Modules\Reports\Livewire\CustomerReport;
use Modules\Reports\Livewire\InventoryReport;
use Modules\Reports\Livewire\SalesReport;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('reports', fn () => redirect()->route('reports.sales'))->name('reports.index');
    Route::get('reports/sales', SalesReport::class)->name('reports.sales');
    Route::get('reports/inventory', InventoryReport::class)->name('reports.inventory');
    Route::get('reports/customers', CustomerReport::class)->name('reports.customers');
});
