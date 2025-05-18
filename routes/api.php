<?php

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;

Route::middleware([EnsureClientIsResourceOwner::class])->group(function () {
    Route::post('cotizador/colectiva', [\App\Http\Controllers\QuoteController::class, 'estimateVehicle']);
});
