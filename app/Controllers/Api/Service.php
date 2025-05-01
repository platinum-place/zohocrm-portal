<?php

namespace App\Controllers\Api;

use App\Libraries\Zoho;
use CodeIgniter\RESTful\ResourceController;

class Service extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        session()->set('cuenta_id', '3222373000092390001');
        session()->set('usuario_id', '3222373000203318001');
    }

    public function index()
    {
        $libreria = new Zoho();

        $criterio = "Corredor:equals:" . session("cuenta_id");
        $planes = $libreria->searchRecordsByCriteria("Products", $criterio);

        $r = [];

        foreach ($planes as $key => $value) {
            $r[][$value->getEntityId()] = $value->getFieldValue('Product_Category');
        }

        return $this->respond($r);
    }

    public function show($id = null)
    {
        $libreria = new Zoho();

        $plan = $libreria->getRecord("Products", $id);

        return $this->respond([
            $plan->getEntityId() => [
                [
                    $plan->getFieldValue('Vendor_Name')->getEntityId() => $plan->getFieldValue('Product_Name')
                ]
            ]
        ]);
    }
}
