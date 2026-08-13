<?php

use Illuminate\Support\Facades\Route;
use Modules\Administration\Http\Controllers\AdministrationController;
use Modules\Administration\Livewire\Users\UserIndex;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('administrations', AdministrationController::class)->names('administration');
    Route::get('users', UserIndex::class)->name('users.index');
});

// Logout
Route::get('logout', [AdministrationController::class, 'logout'])->name('logout');
