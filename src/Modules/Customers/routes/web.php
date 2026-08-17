<?php

use Illuminate\Support\Facades\Route;
use Modules\Customers\Livewire\Customers\CustomerIndex;
use Modules\Customers\Livewire\Customers\CustomerPurchaseHistory;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('customers', CustomerIndex::class)->name('customers.index');
    Route::get('customers/{customer}/history', CustomerPurchaseHistory::class)->name('customers.history');
});
