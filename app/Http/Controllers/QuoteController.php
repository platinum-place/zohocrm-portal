<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quote\EstimateLifeRequest;
use App\Http\Requests\Quote\EstimateVehicleRequest;
use App\Http\Requests\Quote\InspectRequest;
use App\Http\Requests\Quote\IssueLifeRequest;
use App\Http\Requests\Quote\IssueVehicleRequest;
use App\Http\Requests\Quote\ValidateInspectionRequest;

class QuoteController extends Controller
{
    public function estimateVehicle(EstimateVehicleRequest $request)
    {
        return response()->json([
            [
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
                        'descripcion' => 'Cobertura completa del vehículo',
                    ],
                    [
                        'id' => 2,
                        'nombre' => 'Cobertura Total',
                        'descripcion' => 'Cobertura completa del vehículo',
                    ],
                    [
                        'id' => 3,
                        'nombre' => 'Cobertura Total',
                        'descripcion' => 'Cobertura completa del vehículo',
                    ],
                ],
            ]
        ]);
    }

    public function issueVehicle(IssueVehicleRequest $request)
    {
        return response()->noContent();
    }

    public function valueVehicle()
    {
        return response()->json([
            'valorMinimo' => '0000',
            'valorEstandar' => '000.00',
            'valorMaximo' => '000.00',
        ]);
    }

    public function validateInspection(ValidateInspectionRequest $request)
    {
        return response()->noContent();
    }

    public function inspect(InspectRequest $request)
    {
        return response()->noContent();
    }

    public function getQRInspect(ValidateInspectionRequest $request)
    {
        return response()->json([
            'QR' => '0iVBORw0KGgoAAAANSUhEUgAABCQAAAQkCAYAAAClls8JAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAL1ZSURBVHhe7NjBqmvJtiTR9',
        ]);
    }

    public function getQR(ValidateInspectionRequest $request)
    {
        return response()->json([
            'partefrontal' => '0iVBORw0KGgoAAAANSUhEUgAABCQAAAQkCAYAAAClls8JAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAL1ZSURBVHhe7NjBqmvJtiTR9',
        ]);
    }

    public function estimateLife(EstimateLifeRequest $request)
    {
        return response()->json([
            [
                'FechaEmision' => '2025-05-18 10:00:00',
                'FechaVencimiento' => '2026-05-18 10:00:00',
                'Edad' => 30,
                'PlazoAnios' => 5,
                'PlazoDias' => 1825,
                'MontoOriginal' => 1500000.00,
                'NombreCliente' => 'María López',
                'IdenCliente' => '40204567891',
                'FechaNacimiento' => '1995-08-22',
                'Telefono1' => '8095551234',
                'Direccion' => 'Av. Independencia 456, Santo Domingo',
                'Error' => '',
                'codeudor' => true,
                'EdadCodeudor' => 28,
            ]
        ]);
    }

    public function issueLife(IssueLifeRequest $request)
    {
        return response()->noContent();
    }
}
