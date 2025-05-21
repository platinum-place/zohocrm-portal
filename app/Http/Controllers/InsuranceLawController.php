<?php

namespace App\Http\Controllers;

use App\Http\Requests\InsuranceLaw\DisableVehicleLawRequest;
use App\Http\Requests\InsuranceLaw\EstimateVehicleLawRequest;
use App\Http\Requests\InsuranceLaw\SearchDocumentRequest;
use App\Services\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use PHPUnit\Event\Code\Throwable;

class InsuranceLawController
{
    public function __construct(protected ZohoCRMService $crm)
    {
    }

    /**
     * @throws RequestException
     * @throws \Throwable
     * @throws ConnectionException
     */
    public function estimateVehicleLaw(EstimateVehicleLawRequest $request)
 {
        $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Auto))';
        $products = $this->crm->searchRecords('Products', $criteria);

        $response = [];

        foreach ($products['data'] as $product) {
            $alert = '';

            if (in_array($request->get('Actividad'), $product['Restringir_veh_culos_de_uso'])) {
                return "Uso del vehículo restringido.";
            }

            if ((date("Y") - $request->get('Anio')) > $product['Max_antig_edad']) {
                $alert = 'La antigüedad del vehículo es mayor al limite establecido.';
            }

            if ($request->get('MontoOriginal') < $product['Suma_asegurada_min'] || $request->get('MontoOriginal') > $product['Suma_asegurada_max']) {
                $alert = 'El plazo es mayor al limite establecido.';
            }

            try {
                $criteria = "((Marca:equals:" . $request->get('Marca') . ") and (Aseguradora:equals:" . $product['Vendor_Name']['id'] . "))";
                $brands = $this->crm->searchRecords('Restringidos', $criteria);

                foreach ($brands['data'] as $brand) {
                    if (empty($brand['Modelo'])) {
                        $alert = "Marca restrigida.";
                    } elseif ($request->get('Marca') == $brand['Modelo']['id']) {
                        $alert = "Modelo restrigido.";
                    }
                }
            } catch (Throwable $throwable) {

            }

            $taxAmount = 0;

            try {
                $criteria = "Plan:equals:" . $product['id'];
                $taxes = $this->crm->searchRecords('Tasas', $criteria);

                foreach ($taxes['data'] as $tax) {
                    if (in_array($request->get('TipoVehiculo'), $tax['Grupo_de_veh_culo'])) {
                        if (!empty($tax['Suma_limite'])) {
                            if ($request->get('MontoOriginal') >= $tax['Suma_limite']) {
                                if (empty($tax['Suma_hasta'])) {
                                    $taxAmount = $tax['Name'] / 100;
                                } elseif ($request->get('MontoOriginal') < $tax['Suma_hasta']) {
                                    $taxAmount = $tax['Name'] / 100;
                                }
                            }
                        } else {
                            $taxAmount = $tax['Name'] / 100;
                        }
                    }
                }
            } catch (Throwable $throwable) {

            }

            if (!$taxAmount) {
                $alert = 'No se encontraron tasas.';
            }

            $surchargeAmount = 0;

            try {
                $criteria = "((Marca:equals:" . $request->get('Marca') . ") and (Aseguradora:equals:" . $product['Vendor_Name']['id'] . "))";
                $surcharges = $this->crm->searchRecords('Recargos', $criteria);

                foreach ($surcharges['data'] as $surcharge) {
                    $modeloTipo = $request->get('TipoVehiculo');
                    $modeloId = $request->get('Modelo');
                    $ano = $request->get('Marca');

                    $tipo = $surcharge['Tipo'];
                    $modelo = $surcharge['Modelo'];
                    $desde = $surcharge['Desde'];
                    $hasta = $surcharge['Hasta'];

                    $resultado = (
                        ($ano >= $desde && $ano <= $hasta && empty($modelo) && empty($tipo)) ||
                        ($ano >= $desde && $ano <= $hasta && empty($modelo) && $tipo == $modeloTipo) ||
                        ($ano >= $desde && $ano <= $hasta && $modelo == $modeloId && empty($tipo)) ||
                        ($ano >= $desde && $ano <= $hasta && $modelo == $modeloId && $tipo == $modeloTipo) ||
                        (empty($desde) && empty($hasta) && $modelo == $modeloId && $tipo == $modeloTipo) ||
                        (empty($desde) && $ano <= $hasta && $modelo == $modeloId && $tipo == $modeloTipo) ||
                        ($ano >= $desde && empty($hasta) && $modelo == $modeloId && $tipo == $modeloTipo) ||
                        ($ano >= $desde && empty($hasta) && empty($modelo) && empty($tipo)) ||
                        (empty($desde) && $ano <= $hasta && empty($modelo) && empty($tipo)) ||
                        (empty($desde) && empty($hasta) && $modelo == $modeloId && empty($tipo)) ||
                        (empty($desde) && empty($hasta) && empty($modelo) && $tipo == $modeloTipo) ||
                        (empty($desde) && empty($hasta) && empty($modelo) && empty($tipo))
                    );

                    if ($resultado) {
                        $surchargeAmount = $surcharge['Name'] / 100;
                    }
                }
            } catch (Throwable $throwable) {

            }

            $amount = 0;

            if (empty($alert)) {
                $amount = $request->get('MontoAsegurado') * ($taxAmount + ($taxAmount * $surchargeAmount));

                if ($amount > 0 and $amount < $product['Prima_m_nima']) {
                    $amount = $product['Prima_m_nima'];
                }

                $amount = round($amount, 2);
            }

            $data = [
                "Subject" => $request->get('NombreCliente'),
                "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 30 days")),
                "Vigencia_desde" => date("Y-m-d"),
                'Account_Name' => 3222373000092390001,
                'Contact_Name' => 3222373000203318001,
                "Quote_Stage" => "Cotizando",
                "Nombre" => $request->get('NombreCliente'),
                "Fecha_de_nacimiento" => $request->get('FechaNacimiento'),
                "RNC_C_dula" => $request->get('IdCliente'),
                "Correo_electr_nico" => $request->get('Email'),
                "Tel_Celular" => $request->get('TelefMovil'),
                "Tel_Residencia" => $request->get('TelefResidencia'),
                "Tel_Trabajo" => $request->get('TelefTrabajo'),
                "Plan" => 'Mensual Full',
                "Suma_asegurada" => $request->get('MontoAsegurado'),
                "A_o" => $request->get('Anio'),
                "Marca" => $request->get('Marca'),
                "Modelo" => $request->get('Modelo'),
                "Tipo_veh_culo" => $request->get('TipoVehiculo'),
                "Chasis" => $request->get('Chasis'),
                "Placa" => $request->get('Placa'),
                "Fuente" => 'API',
                'Quoted_Items' => [
                    [
                        'Quantity' => 1,
                        'Product_Name' => $product['id'],
                        'Total' => $amount,
                        'Net_Total' => $amount,
                        'List_Price' => $amount,
                    ],
                ],
            ];

            $responseProduct = $this->crm->insertRecords('Quotes', $data);

            $response[] = [
                'Passcode' => null,
                'OfertaID' => null,
                'Prima' => $amount - ($amount * 1.16),
                'Impuesto' => $amount * 0.16,
                'PrimaTotal' => $amount,
                'PrimaCuota' => null,
                'Planid' => $product['id'],
                'Plan' => 'Plan Mensual Full',
                'Aseguradora' => $product['Vendor_Name']['name'],
                'Idcotizacion' => $responseProduct['data'][0]['details']['id'],
                'Fecha' => now()->toDateTimeString(),
                'CoberturasList' => null,
                'Alerta' => $alert,
            ];
        }

        return response()->json($response);
    }

    public function paymentType()
    {
        return response()->json([
            [
                'ID' => 1,
                'TIPOVEHICULOID' => 1.0,
                'TIPOVEHICULO' => 'Motocicletas hasta 250 CC',
                'COBERTURADPAYRC' => '50/50/100',
                'FINANZAJUDICIAL' => '50000',
            ],
        ]);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     * @throws \Throwable
     */
    public function searchDocument(SearchDocumentRequest $request, string $identification)
    {
        $criteria = "((RNC_C_dula:equals:$identification) and (Plan:equals:Auto))";
        $quotes = $this->crm->searchRecords('Quotes', $criteria);

        $response = [];

        foreach ($quotes['data'] as $quote) {
            $response[] = [
                'IDCliente' => $identification,
                'NombreCliente' => $quote['Nombre'],
                'Direccion' => $quote['Direcci_n'],
                'Telefono' => $quote['Tel_Residencia'],
                'IDTipoVehiculo' => null,
                'TipoVehiculo' => $quote['Tipo_veh_culo'],
                'Marca' => $quote['Marca']['name'],
                'Modelo' => $quote['Modelo']['name'],
                'Correo' => $quote['Correo_electr_nico'],
                'Anio' => $quote['A_o'],
                'Color' => $quote['Color'],
                'Chassis' => $quote['Chasis'],
                'Placa' => $quote['Placa'],
                'Poliza' => null,
                'Prima' => $quote['Created_Time'],
                'Vigencia' => '12 Meses',
                'Cobertura' => null,
                'FianzaJudicial' => null,
                'FechaEmision' => $quote['Created_Time'],
                'Estado' => $quote['Quote_Stage'] === 'Cancelada' ? 0 : 1,
                'Usuario' => null,
                'PDV' => null,
                'C3_Meses' => null,
                'C6_Meses' => null,
                'C12_Meses' => null,
                'Error' => null,
            ];
        }

        return response()->json($response);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     * @throws \Throwable
     */
    public function disableVehicleLaw(DisableVehicleLawRequest $request, string $id)
    {
        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (\Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404
            ], 404);
        }

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];
        $this->crm->updateRecords('Quotes', $id, $data);

        return response()->noContent(200);
    }
}
