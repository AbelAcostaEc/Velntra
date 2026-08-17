<?php

use Illuminate\Support\Facades\Route;
use Modules\Customers\Livewire\Customers\CustomerIndex;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('customers', CustomerIndex::class)->name('customers.index');
});
