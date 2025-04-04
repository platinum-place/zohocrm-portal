<?php

namespace App\Controllers\Api;

use App\Libraries\Cotizar\CotizarAuto;
use App\Libraries\Cotizar\CotizarDesempleo;
use App\Libraries\Cotizar\CotizarIncendio;
use App\Libraries\Cotizar\CotizarIncendio2;
use App\Libraries\Cotizar\CotizarVida;
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

        $libreria = new Zoho();
        $quotes = array();

        foreach ($cotizacion->planes as $plan) {
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
            $id = $libreria->createRecords("Quotes", $registro, $cotizacion->planes);

            $quotes[] = [
                'Passcode' => '',
                'OfertaID' => '',
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

        foreach ($cotizacion->getLineItems() as $lineItem) {
            $id_plan = $lineItem->getProduct()->getEntityId();
        }

        $libreria->actualizar_cotizacion($cotizacion, $id_plan);

        return $this->respond(['code' => 200, 'status' => 'success']);
    }

    public function estimateLife()
    {
        $libreria = new \App\Libraries\Cotizaciones();

        $cotizacion = new Cotizacion();

        $data = $this->request->getPost();

        if (!isset($data['EdadCodeudor'])) {
            $data['EdadCodeudor'] = null;
        } else {
            $anioActual = (int)date('Y');
            $anioNacimiento = $anioActual - $data['EdadCodeudor'];
            $mes = rand(1, 12);
            $dia = rand(1, 28);
            $data['EdadCodeudor'] = sprintf('%04d-%02d-%02d', $anioNacimiento, $mes, $dia);
        }

        if (!isset($data['codeudor'])) {
            $data['codeudor'] = null;
        }

        $cotizacion->suma = $data['MontoOriginal'];
        $cotizacion->plan = 'Vida';
        $cotizacion->plazo = $data['PlazoAnios'] * 12;
        $cotizacion->fecha_deudor = $data['FechaNacimiento'];
        $cotizacion->fecha_codeudor = $data['EdadCodeudor'];

        $cotizar = new CotizarVida($cotizacion, $libreria);

        $cotizar->cotizar_planes();

        if (empty($cotizacion->planes)) {
            throw new \Exception("No se encontraron planes");
        }

        $quotes = array();
        $libreria = new Zoho();

        foreach ($cotizacion->planes as $plan) {
            $registro = [
                "Subject" => $data['NombreCliente'],
                "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 30 days")),
                "Vigencia_desde" => date("Y-m-d"),
                "Account_Name" => session('cuenta_id'),
                "Contact_Name" => session('usuario_id'),
                "Quote_Stage" => "Cotizando",
                "Nombre" => $data['NombreCliente'],
                "Fecha_de_nacimiento" => $data['FechaNacimiento'],
                "RNC_C_dula" => $data['IdenCliente'],
                "Direcci_n" => $data['Direccion'],
                "Tel_Celular" => $data['Telefono1'],
                "Plan" => 'Vida',
                "Suma_asegurada" => $data['MontoOriginal'],
                "Plazo" => $data['PlazoAnios'] * 12,
                "Nombre_codeudor" => $data['codeudor'],
                "Fecha_de_nacimiento_codeudor" => $data['EdadCodeudor'],
            ];

            $id = $libreria->createRecords("Quotes", $registro, [$plan]);

            $quotes[] = [
                'Impuesto' => round($plan['neta'], 2),
                'PrimaPeriodo' => '',
                'PrimaTotal' => round($plan['total'], 2),
                'identificador' => $id,
                'Aseguradora' => $plan['aseguradora'],
                'MontoOrig' => $data['MontoOriginal'],
                'Anios' => $data['PlazoAnios'],
                'EdadTerminar' => $data['Edad'] + $data['PlazoAnios'],
                'Cliente' => $data['NombreCliente'],
                'Fecha' => date('Y-m-d'),
                'Direccion' => $data['Direccion'],
                'Edad' => $data['Edad'],
                'IdenCliente' => $data['IdenCliente'],
                'Codeudor' => $data['codeudor'],
            ];
        }

        return $this->respond($quotes);
    }

    public function issueLife()
    {
        if (!$this->request->getPost()) {
            throw new \Exception("No se recibieron datos");
        }

        $libreria = new \App\Libraries\Cotizaciones();

        $data = $this->request->getPost();

        $cotizacion = $libreria->getRecord("Quotes", $data['Identificador']);

        foreach ($cotizacion->getLineItems() as $lineItem) {
            $id_plan = $lineItem->getProduct()->getEntityId();
        }

        $libreria->actualizar_cotizacion($cotizacion, $id_plan);

        return $this->respond(['code' => 200, 'status' => 'success']);
    }
}