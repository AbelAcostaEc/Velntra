<?php

use Illuminate\Support\Facades\Route;
use Modules\Sales\Livewire\Pos\PosIndex;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('sales', PosIndex::class)->name('sales.index');
});
