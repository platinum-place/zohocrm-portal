<?php

namespace App\Controllers\Api;

use App\Libraries\Cotizar\CotizarAuto;
use App\Libraries\Zoho;
use App\Models\Cotizacion;
use CodeIgniter\RESTful\ResourceController;
use zcrmsdk\crm\exception\ZCRMException;

class Quote extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        session()->set('cuenta_id', '3222373000092390001');
        session()->set('usuario_id', '3222373000203318001');
    }

    public function estimateVehicle()
    {
        if (!$this->request->getPost()) {
            throw new \Exception("No se recibieron datos");
        }

        $libreria = new \App\Libraries\Cotizaciones();
        $cotizacion = new Cotizacion();

        $data = $this->request->getPost();

        $cotizacion->suma = $data["MontoAsegurado"];
        $cotizacion->plan = 'Anual full';
        $cotizacion->ano = $data["Anio"];
        $cotizacion->uso = $data["Actividad"];
        $cotizacion->marcaid = $data["Marca"];
        $cotizacion->modeloid = $data["Modelo"];
        $cotizacion->modelotipo = $data["TipoVehiculo"];

        $cotizar = new CotizarAuto($cotizacion, $libreria);

        $cotizar->cotizar_planes();

        if (empty($cotizacion->planes)) {
            throw new \Exception("No se encontraron planes");
        }

        $registro = [
            "Subject" => $data['NombreCliente'],
            "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 30 days")),
            "Vigencia_desde" => date("Y-m-d"),
            "Account_Name" => session('cuenta_id'),
            "Contact_Name" => session('usuario_id'),
            "Quote_Stage" => "Cotizando",
            "Nombre" => $data['NombreCliente'],
            "Fecha_de_nacimiento" => $data['FechaNacimiento'],
            "RNC_C_dula" => (string)$data['IdCliente'],
            "Correo_electr_nico" => $data['Email'],
            "Tel_Celular" => $data['TelefMovil'],
            "Tel_Residencia" => $data['TelefResidencia'],
            "Tel_Trabajo" => $data['TelefTrabajo'],
            "Plan" => 'Anual full',
            "Suma_asegurada" => $data['MontoAsegurado'],
            "A_o" => $data['Anio'],
            "Marca" => $data['Marca'],
            "Modelo" => $data['Modelo'],
            "Tipo_veh_culo" => $data['TipoVehiculo'],
            "Chasis" => $data['Chasis'],
            "Placa" => $data['Placa'],
        ];

        $libreria = new Zoho();
        $id = $libreria->createRecords("Quotes", $registro, $cotizacion->planes);

        $quotes = array();

        foreach ($cotizacion->planes as $plan) {
            $quotes[] = [
                'Passcode' => '',
                'OfertaID' => $plan['planid'],
                'Prima' => round($plan['prima'], 2),
                'Impuesto' => round($plan['neta'], 2),
                'PrimaTotal' => round($plan['total'], 2),
                'PrimaCuota' => round($plan['total'] / 12, 2),
                'Planid' => '',
                'Plan' => 'Plan Anual Full',
                'Aseguradora' => $plan['aseguradora'],
                'Idcotizacion' => $id,
                'Fecha' => date('Y-m-d'),
                'CoberturasList' => '',
                'Error' => '',
                'Alerta' => $plan['comentario'],
            ];
        }

        return $this->respond($quotes);
    }

    public function issuePolicy()
    {
        if (!$this->request->getPost()) {
            throw new \Exception("No se recibieron datos");
        }

        $libreria = new \App\Libraries\Cotizaciones();

        $data = $this->request->getPost();

        $cotizacion = $libreria->getRecord("Quotes", $data['cotzid']);
        $libreria->actualizar_cotizacion($cotizacion, $data['ofertaID']);

        return $this->respond(['code' => 200, 'status' => 'success']);
    }
}