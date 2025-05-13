<?php

namespace App\Controllers\Api;

use App\Libraries\Zoho;
use CodeIgniter\RESTful\ResourceController;

class Vehicle extends ResourceController
{
    protected $format = 'json';

    public function brands()
    {
        $libreria = new Zoho();

        $marcas = $libreria->getRecords("Marcas");

        $marcas_sort = array();

        foreach ($marcas as $marca) {
            $marcas_sort[][$marca->getEntityId()] = $marca->getFieldValue('Name');
        }

        usort($marcas_sort, function ($a, $b) {
            return strcmp(reset($a), reset($b));
        });

        return $this->respond($marcas_sort);
    }

    public function Modelos($brand_id = null)
    {
        if (!$brand_id) {
            throw new \Exception("No se recibieron datos");
        }

        $pag = 1;
        $libreria = new Zoho();
        $criteria = "Marca:equals:" . $brand_id;
        $models = [];
        do {
            $modelos = $libreria->searchRecordsByCriteria("Modelos", $criteria, $pag);

            if (!empty($modelos)) {
                $modelos_sort = array();

                foreach ($modelos as $modelo) {
                    $modelos_sort[] = [
                        'id' => $modelo->getEntityId(),
                        'name' => $modelo->getFieldValue('Name'),
                        'tipo' => $modelo->getFieldValue('Tipo'),
                    ];
                }

                usort($modelos_sort, function ($a, $b) {
                    return strcmp($a['name'], $b['name']);
                });

                $pag++;
                foreach ($modelos_sort as $modelo_sort) {
                    $models[][$brand_id] = [$modelo_sort['id'] => $modelo_sort['name']];
                }
            } else {
                $pag = 0;
            }
        } while ($pag > 0);

        return $this->respond($models);
    }

    public function TipoVehiculo()
    {
        $types = [
            "01" => 'Automóvil',
            "02" => 'Jeepeta',
            "03" => 'Camioneta',
            "04"=> 'Furgoneta',
            "05"=> 'Minivan',
            "06" => 'Camión',
            "07" => 'Veh. Pesado',
            "08" => 'Autobús',
            "09" => 'Minibus'
        ];
        return $this->respond($types);
    }

    public function Accesorios()
    {
        $types = [
            "01" => 'Gas',
            "02" => 'Aros',
        ];
        return $this->respond($types);
    }

    public function Actividades()
    {
        $types = [
            "01" => 'Uber',
            "02" => 'Taxi',
        ];
        return $this->respond($types);
    }

    public function Circulacion()
    {
        $types = [
            "01" => 'Distrito Nacional',
            "02" => 'Santo Domingo',
        ];
        return $this->respond($types);
    }

    public function Color()
    {
        $types = [
            "01" => 'Azul',
            "02" => 'Rojo',
        ];
        return $this->respond($types);
    }
}
