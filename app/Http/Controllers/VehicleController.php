<?php

namespace App\Http\Controllers;

use App\Services\Zoho\ZohoVehicle;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class VehicleController extends Controller
{
    public function __construct(protected ZohoVehicle $zohoVehicle)
    {
    }

    /**
     * Get list of brands from Zoho
     */
    public function list()
    {
        $brands = $this->zohoVehicle->brandList();

        $sortedBrands = collect($brands['data'])
            ->map(fn($brand) => [$brand['id'] => $brand['Name']])
            ->sortBy(fn($brand) => reset($brand))
            ->values()
            ->toArray();

        return response()->json($sortedBrands);
    }

    public function getModel(string $brandId)
    {
        $page = 1;
        $models = [];
        try {
            do {
                $modelsData = $this->zohoVehicle->modelsList($brandId, $page);

                if (!empty($modelsData)) {
                    $sortedModels = collect($modelsData['data'])
                        ->map(fn($model) => [
                            'id' => $model['id'],
                            'name' => $model['Name'],
                            'type' => $model['Tipo']
                        ])
                        ->sortBy('name')
                        ->map(fn($model) => [$brandId => [$model['id'] => $model['name']]])
                        ->values()
                        ->toArray();

                    $models = array_merge($models, $sortedModels);
                    $page++;
                } else {
                    $page = 0;
                }
            } while ($page > 0);
        } catch (Throwable $e) {

        }
        return response()->json($models);
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
