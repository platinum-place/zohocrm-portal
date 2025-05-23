<?php

namespace App\Http\Controllers;

use App\Services\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;

class VehicleController extends Controller
{
    public function __construct(protected ZohoCRMService $crm) {}

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function list()
    {
        $fields = ['id', 'Name'];
        $response = $this->crm->getRecords('Marcas', $fields);

        $brands = collect($response['data'])
            ->map(fn ($brand) => [
                'IdMarca' => (int) $brand['id'],
                'Marca' => $brand['Name'],
            ])
            ->sortBy(fn ($brand) => reset($brand))
            ->values()
            ->toArray();

        return response()->json($brands);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function getModel(string $brandId)
    {
        $criteria = "Marca:equals:$brandId";
        $response = $this->crm->searchRecords('Modelos', $criteria);

        $models = collect($response['data'])
            ->map(fn ($model) => [
                'IdMarca' => (int) $model['Marca']['id'],
                'IdModelo' => (int) $model['id'],
                'Modelo' => $model['Name'],
            ])
            ->sortBy(fn ($model) => reset($model))
            ->values()
            ->toArray();

        return response()->json($models);
    }

    public function typeList()
    {
        $types = [
            [
                'IdTipoVehiculo' => 1,
                'TipoVehiculo' => 'Automóvil',
            ],
            [
                'IdTipoVehiculo' => 2,
                'TipoVehiculo' => 'Camioneta',
            ],
            [
                'IdTipoVehiculo' => 3,
                'TipoVehiculo' => 'Camión',
            ],
        ];

        return response()->json($types);
    }

    public function accessoriesList()
    {
        $accessories = [
            [
                'IdAccesorio' => 2,
                'Accesorio' => 'Cambio de Guia',
            ],
            [
                'IdAccesorio' => 3,
                'Accesorio' => 'LOVATO',
            ],
            [
                'IdAccesorio' => 1,
                'Accesorio' => 'OTROS EQUIPO DE GAS',
            ],
            [
                'IdAccesorio' => 5,
                'Accesorio' => 'ROMANO',
            ],
            [
                'IdAccesorio' => 6,
                'Accesorio' => 'SISTEMA DE GAS NATURAL APROBADO',
            ],
        ];

        return response()->json($accessories);
    }

    public function activitiesList()
    {
        $activities = [
            [
                'IdActividad' => 1,
                'Actividad' => 'Uber',
            ],
            [
                'IdActividad' => 2,
                'Actividad' => 'Taxi',
            ],
        ];

        return response()->json($activities);
    }

    public function routeList()
    {
        $routes = [
            [
                'IdCirculacion' => 1,
                'circulacion' => 'AZUA',
            ],
            [
                'IdCirculacion' => 2,
                'circulacion' => 'BAHORUCO',
            ],
            [
                'IdCirculacion' => 3,
                'circulacion' => 'BARAHONA',
            ],
            [
                'IdCirculacion' => 4,
                'circulacion' => 'DAJABON',
            ],
            [
                'IdCirculacion' => 5,
                'circulacion' => 'DISTRITO NACIONAL',
            ],
            [
                'IdCirculacion' => 0,
                'circulacion' => 'DISTRITO NACIONAL',
            ],
        ];

        return response()->json($routes);
    }

    public function colorList()
    {
        $colors = [
            [
                'IdColor' => 2,
                'Color' => 'Amarillo',
            ],
            [
                'IdColor' => 3,
                'Color' => 'Azul',
            ],
            [
                'IdColor' => 4,
                'Color' => 'Azul Agua',
            ],
            [
                'IdColor' => 5,
                'Color' => 'Azul Cielo',
            ],
        ];

        return response()->json($colors);
    }
}
