<?php

namespace App\Http\Controllers;

class VehicleController extends Controller
{
    public function list()
    {
        return response()->json([
            '01' => 'TOYOTA',
            '02' => 'RENAULT',
        ]);
    }

    public function getModel(string $MarcaID)
    {
        return response()->json([
            '02' => [
                '01' => 'LOGAN',
                '02' => 'SANDERO',
            ],
        ]);
    }

    public function typeList()
    {
        return response()->json([
            '01' => 'AUTO',
            '02' => 'CAMIONETA',
        ]);
    }

    public function accessoriesList()
    {
        return response()->json([
            '01' => 'Gas',
            '02' => 'Aros',
        ]);
    }

    public function activitiesList()
    {
        return response()->json([
            '01' => 'Uber',
            '02' => 'Taxi',
        ]);
    }

    public function routeList()
    {
        return response()->json([
            '01' => 'Distrito Nacional',
            '02' => 'Santo Domingo',
        ]);
    }

    public function colorList()
    {
        return response()->json([
            '01' => 'Azul',
            '02' => 'Rojo',
        ]);
    }
}
