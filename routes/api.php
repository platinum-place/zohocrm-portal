<?php

use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Middleware\EnsureClientIsResourceOwner;

Route::middleware([EnsureClientIsResourceOwner::class])->group(function () {
    Route::post('cotizador/colectiva', [\App\Http\Controllers\QuoteController::class, 'estimateVehicle']);
    Route::post('cotizador/EmitirAuto', [\App\Http\Controllers\QuoteController::class, 'issueVehicle']);
    Route::get('cotizador/ValorPromedio', [\App\Http\Controllers\QuoteController::class, 'valueVehicle']);
    Route::post('cotizador/ValidarInspeccion', [\App\Http\Controllers\QuoteController::class, 'validateInspection']);
    Route::post('cotizador/Inspeccionar', [\App\Http\Controllers\QuoteController::class, 'inspect']);
    Route::post('cotizador/ObtenerQRInspeccion', [\App\Http\Controllers\QuoteController::class, 'getQRInspect']);
    Route::post('cotizador/ObtenerImagenes', [\App\Http\Controllers\QuoteController::class, 'getQR']);
    Route::post('cotizador/CotizaVida', [\App\Http\Controllers\QuoteController::class, 'estimateLife']);
    Route::post('cotizador/EmitirVida', [\App\Http\Controllers\QuoteController::class, 'issueLife']);
    Route::post('cotizador/CotizaDesempleoDeuda', [\App\Http\Controllers\QuoteController::class, 'estimateUnemploymentDebt']);
    Route::post('cotizador/EmitirDesempleoDeuda', [\App\Http\Controllers\QuoteController::class, 'issueUnemploymentDebt']);
    Route::post('cotizador/CotizaDesempleo', [\App\Http\Controllers\QuoteController::class, 'estimateUnemployment']);
    Route::post('cotizador/EmitirDesempleo', [\App\Http\Controllers\QuoteController::class, 'issueUnemployment']);

    Route::post('vehiculos/Marca', [\App\Http\Controllers\VehicleController::class, 'list']);
    Route::post('vehiculos/Modelos/{MarcaID}', [\App\Http\Controllers\VehicleController::class, 'getModel']);
    Route::post('vehiculos/TipoVehiculo', [\App\Http\Controllers\VehicleController::class, 'typeList']);
    Route::post('vehiculos/Accesorios', [\App\Http\Controllers\VehicleController::class, 'accessoriesList']);
    Route::post('vehiculos/Actividades', [\App\Http\Controllers\VehicleController::class, 'activitiesList']);
    Route::post('vehiculos/Circulacion', [\App\Http\Controllers\VehicleController::class, 'routeList']);
    Route::get('vehiculos/Color', [\App\Http\Controllers\VehicleController::class, 'colorList']);

    Route::get('Productos', [\App\Http\Controllers\ProductController::class, 'list']);
    Route::get('Productos/Aseguradoras/{id}', [\App\Http\Controllers\ProductController::class, 'show']);
});
