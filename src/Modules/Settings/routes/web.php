<?php

use Illuminate\Support\Facades\Route;
use Modules\Settings\Livewire\Settings\SettingIndex;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('settings', SettingIndex::class)->name('settings.index');
});
