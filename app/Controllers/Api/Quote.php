<?php

namespace App\Controllers\Api;

use App\Libraries\Cotizar\CotizarAuto;
use App\Libraries\Cotizar\CotizarDesempleo;
use App\Libraries\Cotizar\CotizarIncendio;
use App\Libraries\Cotizar\CotizarVida;
use App\Libraries\Zoho;
use App\Models\Cotizacion;
use CodeIgniter\RESTful\ResourceController;

class Quote extends ResourceController
{
    protected $format = 'json';

    public function __construct()
    {
        session()->set('cuenta_id', '3222373000092390001');
        session()->set('usuario_id', '3222373000203318001');
    }

    public function colectiva()
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
                "Plan" => $plan['plan'],
                "Suma_asegurada" => $data['MontoAsegurado'],
                "A_o" => $data['Anio'],
                "Marca" => $data['Marca'],
                "Modelo" => $data['Modelo'],
                "Tipo_veh_culo" => $data['TipoVehiculo'],
                "Chasis" => $data['Chasis'],
                "Placa" => $data['Placa'],
                "Fuente" => 'API',
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
                'Plan' => $plan['plan'],
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

    public function EmitirAuto()
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

    public function CotizaVida()
    {
        $libreria = new \App\Libraries\Cotizaciones();

        $cotizacion = new Cotizacion();

        $data = $this->request->getPost();

        if (!isset($data['EdadCodeudor'])) {
            $data['EdadCodeudor'] = null;
        } else {
            $anioActual = (int)date('Y');
            $anioNacimiento = $anioActual - $data['EdadCodeudor'];
            $mes = 1;
            $dia = 1;
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
                "Fuente" => 'API',
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

    public function EmitirVida()
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

    public function CotizaDesempleoDeuda()
    {
        $libreria = new \App\Libraries\Cotizaciones();

        $cotizacion = new Cotizacion();

        $data = $this->request->getPost();

        $cotizacion->suma = $data['MontoOriginal'];
        $cotizacion->plan = 'Vida/Desempleo';
        $cotizacion->plazo = $data['Plazo'] * 12;
        $anioActual = (int)date('Y');
        $anioNacimiento = $anioActual - $data['TiempoLaborando'];
        $mes = 1;
        $dia = 1;
        $data['TiempoLaborando'] = sprintf('%04d-%02d-%02d', $anioNacimiento, $mes, $dia);
        $cotizacion->fecha_deudor = $data['TiempoLaborando'];

        $cotizar = new CotizarDesempleo($cotizacion, $libreria);

        $cotizar->cotizar_planes();

        if (empty($cotizacion->planes)) {
            throw new \Exception("No se encontraron planes");
        }

        $quotes = array();
        $libreria = new Zoho();

        foreach ($cotizacion->planes as $plan) {
            $registro = [
                "Subject" => $data['Cliente'],
                "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 30 days")),
                "Vigencia_desde" => date("Y-m-d"),
                "Account_Name" => session('cuenta_id'),
                "Contact_Name" => session('usuario_id'),
                "Quote_Stage" => "Cotizando",
                "Nombre" => $data['Cliente'],
                "RNC_C_dula" => $data['IdenCliente'],
                "Direcci_n" => $data['Direccion'],
                "Tel_Celular" => $data['Telefono'],
                "Plan" => 'Vida/Desempleo',
                "Suma_asegurada" => $data['MontoOriginal'],
                "Plazo" => $data['Plazo'] * 12,
                "Fuente" => 'API',
            ];

            $id = $libreria->createRecords("Quotes", $registro, [$plan]);

            $quotes[] = [
                'Impuesto' => round($plan['neta'], 2),
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'identificador' => $id,
                'Cliente' => $data['Cliente'],
                'Direccion' => $data['Direccion'],
                'TipoEmpleado' => 'Publico',
                'Fecha' => date('Y-m-d'),
                'IdenCliente' => $data['IdenCliente'],
                'Telefono' => $data['Telefono'],
                'Aseguradora' => $plan['aseguradora'],
                'MontoPrestamo' => '000.00',
                'Cuota' => '000.00',
                'PlazoMese' => $data['Plazo'] * 12,
                'Desempleo' => '000.00',
                'Deuda' => '000.00',
                'Total' => round($plan['total'], 2),
            ];
        }

        return $this->respond($quotes);
    }

    public function CotizaDesempleo()
    {
        $libreria = new \App\Libraries\Cotizaciones();

        $cotizacion = new Cotizacion();

        $data = $this->request->getPost();

        $cotizacion->suma = $data['MontoOriginal'];
        $cotizacion->plan = 'Vida/Desempleo';
        $cotizacion->plazo = $data['Plazo'] * 12;
        $anioActual = (int)date('Y');
        $anioNacimiento = $anioActual - $data['TiempoLaborando'];
        $mes = 1;
        $dia = 1;
        $data['TiempoLaborando'] = sprintf('%04d-%02d-%02d', $anioNacimiento, $mes, $dia);
        $cotizacion->fecha_deudor = $data['TiempoLaborando'];
        $cotizacion->cuota = $data['Cuota'];

        $cotizar = new CotizarDesempleo($cotizacion, $libreria);

        $cotizar->cotizar_planes();

        if (empty($cotizacion->planes)) {
            throw new \Exception("No se encontraron planes");
        }

        $quotes = array();
        $libreria = new Zoho();

        foreach ($cotizacion->planes as $plan) {
            $registro = [
                "Subject" => $data['Cliente'],
                "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 30 days")),
                "Vigencia_desde" => date("Y-m-d"),
                "Account_Name" => session('cuenta_id'),
                "Contact_Name" => session('usuario_id'),
                "Quote_Stage" => "Cotizando",
                "Nombre" => $data['Cliente'],
                "RNC_C_dula" => $data['IdenCliente'],
                "Direcci_n" => $data['Direccion'],
                "Tel_Celular" => $data['Telefono'],
                "Plan" => 'Vida/Desempleo',
                "Suma_asegurada" => $data['MontoOriginal'],
                "Plazo" => $data['Plazo'] * 12,
                "Cuota" => $data['Cuota'],
                "Fuente" => 'API',
            ];

            $id = $libreria->createRecords("Quotes", $registro, [$plan]);

            $quotes[] = [
                'Impuesto' => round($plan['neta'], 2),
                'PrimaPeriodo' => '',
                'PrimaTotal' => '',
                'identificador' => $id,
                'Cliente' => $data['Cliente'],
                'Direccion' => $data['Direccion'],
                'Fecha' => date('Y-m-d'),
                'TipoEmpleado' => '',
                'IdenCliente' => $data['IdenCliente'],
                'Aseguradora' => $plan['aseguradora'],
                'MontoOriginal' => $data['MontoOriginal'],
                'Cuota' => $data['Cuota'],
                'PlazoMese' => $data['Plazo'] * 12,
                'Total' => round($plan['total'], 2),
            ];
        }

        return $this->respond($quotes);
    }

    public function CotizaIncendio()
    {
        $libreria = new \App\Libraries\Cotizaciones();

        $cotizacion = new Cotizacion();

        $data = $this->request->getPost();

        $cotizacion->suma = $data['ValorFinanciado'];
        $cotizacion->plan = 'Seguro Incendio Hipotecario';
        $cotizacion->plazo = $data['Plazo'] * 12;
        $anioActual = (int)date('Y');
        $anioNacimiento = $anioActual - $data['TiempoLaborando'];
        $mes = 1;
        $dia = 1;
        $data['TiempoLaborando'] = sprintf('%04d-%02d-%02d', $anioNacimiento, $mes, $dia);
        $cotizacion->fecha_deudor = $data['TiempoLaborando'];
        $cotizacion->direccion = $data['Ubicación'];
        $cotizacion->prestamo = $data['MontoOriginal'];
        $cotizacion->construccion = 'Superior';
        $cotizacion->riesgo = 'Vivienda';

        $cotizar = new CotizarIncendio($cotizacion, $libreria);

        $cotizar->cotizar_planes();

        if (empty($cotizacion->planes)) {
            throw new \Exception("No se encontraron planes");
        }

        $quotes = array();
        $libreria = new Zoho();

        foreach ($cotizacion->planes as $plan) {
            $registro = [
                "Subject" => $data['Cliente'],
                "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 30 days")),
                "Vigencia_desde" => date("Y-m-d"),
                "Account_Name" => session('cuenta_id'),
                "Contact_Name" => session('usuario_id'),
                "Quote_Stage" => "Cotizando",
                "Nombre" => $data['Cliente'],
                "RNC_C_dula" => $data['IdentCliente'],
                "Tel_Celular" => $data['Telefono'],
                "Plan" => 'Seguro Incendio Hipotecario',
                "Suma_asegurada" => $data['ValorFinanciado'],
                "Plazo" => $data['Plazo'] * 12,
                "Cuota" => $data['Cuota'],
                "Fuente" => 'API',
            ];

            $id = $libreria->createRecords("Quotes", $registro, [$plan]);

            $quotes[] = [
                'Impuesto' => round($plan['neta'], 2),
                'PrimaPeriodo' => '',
                'PrimaTotal' => round($plan['total'], 2),
                'PrimaVida' => '',
                'PrimaTotalVida' => '',
                'Direccion' => '',
                'identificador' => $id,
                'Aseguradora' => '',
                'Anios' => '',
                'Valor' => '',
                'EdadTerminar' => '',
                'Codeudor' => '',
                'Edad Codeudor' => '',
                'IdentiCodeudor' => '',
                'CoberturasListInc' => '',
                'CoberturasListVid' => '',
            ];
        }

        return $this->respond($quotes);
    }

    public function ValorPromedio()
    {
        $types = [
            'valorMinimo' => '0000',
            'valorEstandar' => '000.00',
            'valorMaximo' => '000.00',
        ];
        return $this->respond($types);
    }

    public function ValidarInspeccion()
    {
        if (!$this->request->getRawInput()) {
            throw new \Exception("No se recibieron datos");
        }

        $libreria = new \App\Libraries\Cotizaciones();

        $data = $this->request->getRawInput();

        $cambios = [
            "Depurado" => true,
        ];

        $libreria->update("Quotes", $data['cotz_id'], $cambios);

        return $this->respond(['code' => 200, 'status' => 'success']);
    }

    public function GetTipoEmpleado()
    {
        $types = [
            '1' => 'Publico',
            '2' => 'Privado',
        ];
        return $this->respond($types);
    }

    public function GetGiroDelNegocio()
    {
        $types = [
            '1' => 'COMERCIAL',
            '2' => 'CASA DE CAMPO',
        ];
        return $this->respond($types);
    }

    public function CancelarVida()
    {
        if (!$this->request->getRawInput()) {
            throw new \Exception("No se recibieron datos");
        }

        $libreria = new \App\Libraries\Cotizaciones();

        $data = $this->request->getRawInput();

        $cambios = [
            "Quote_Stage" => "Cancelada",
        ];

        $libreria->update("Quotes", $data['Identificador'], $cambios);

        return $this->respond(['code' => 200, 'status' => 'success']);
    }

    public function CancelarAuto()
    {
        if (!$this->request->getRawInput()) {
            throw new \Exception("No se recibieron datos");
        }

        $libreria = new \App\Libraries\Cotizaciones();

        $data = $this->request->getRawInput();

        $cambios = [
            "Quote_Stage" => "Cancelada",
        ];

        $libreria->update("Quotes", $data['IdCotizacion'], $cambios);

        return $this->respond(['code' => 200, 'status' => 'success']);
    }
}