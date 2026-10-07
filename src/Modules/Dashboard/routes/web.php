<?php

use Illuminate\Support\Facades\Route;
use Modules\Dashboard\Livewire\DashboardIndex;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', DashboardIndex::class)->name('dashboard');
});
