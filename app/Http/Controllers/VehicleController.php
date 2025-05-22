<?php

namespace App\Http\Controllers;

use App\Services\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class VehicleController extends Controller
{
    public function __construct(protected ZohoCRMService $crm) {}

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function list()
    {
        $fields = ['id', 'Name'];
        $brands = $this->crm->getRecords('Marcas', $fields);

        $sortedBrands = collect($brands['data'])
            ->map(fn ($brand) => [$brand['id'] => $brand['Name']])
            ->sortBy(fn ($brand) => reset($brand))
            ->values()
            ->toArray();

        return response()->json($sortedBrands);
    }

    public function getModel(string $brandId)
    {
        $page = 1;
        $models = [];
        $criteria = "Marca:equals:$brandId";
        $modelsData = $this->crm->searchRecords('Modelos', $criteria);

        $modelos_sort = array();

        foreach ($modelsData['data'] as $modelo) {
            $modelos_sort[] = [
                'id' => $modelo['id'],
                'name' => $modelo['Name'],
                'tipo' => $modelo['Tipo'],
            ];
        }

        usort($modelos_sort, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        foreach ($modelos_sort as $modelo_sort) {
            $models[][$brandId] = [$modelo_sort['id'] => $modelo_sort['name']];
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
