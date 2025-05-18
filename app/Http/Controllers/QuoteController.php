<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVehicleRequest;
use Illuminate\Http\Request;

class QuoteController extends Controller
{
    public function estimateVehicle(StoreVehicleRequest $request)
    {
        $response = [
            'Passcode' => '4821',
            'OfertaID' => 105,
            'Prima' => 12500.75,
            'Impuesto' => 1875.11,
            'PrimaTotal' => 14375.86,
            'PrimaCuota' => 1197.99,
            'Planid' => 3,
            'Plan' => 'Plan Básico',
            'Aseguradora' => 'Seguros XYZ',
            'Idcotizacion' => 789654,
            'Fecha' => now()->toDateTimeString(),
            'CoberturasList' => [
                [
                    'id' => 1,
                    'nombre' => 'Cobertura Total',
                    'descripcion' => 'Cobertura completa del vehículo'
                ],
                [
                    'id' => 2,
                    'nombre' => 'Cobertura Total',
                    'descripcion' => 'Cobertura completa del vehículo'
                ],
                [
                    'id' => 3,
                    'nombre' => 'Cobertura Total',
                    'descripcion' => 'Cobertura completa del vehículo'
                ],
            ],
        ];

        return response()->json($response);
    }
}
