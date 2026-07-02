<?php

use Illuminate\Support\Facades\Route;
use Modules\Administration\Http\Controllers\AdministrationController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('administrations', AdministrationController::class)->names('administration');
});
