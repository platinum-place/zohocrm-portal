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
        $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Vida))';
        $products = $this->crm->searchRecords('Products', $criteria);

        $response = [];

        foreach ($products['data'] as $product) {
            $response[] = [
                'Passcode' => '4821',
                'OfertaID' => 105,
                'Prima' => 12500.75,
                'Impuesto' => 1875.11,
                'PrimaTotal' => 14375.86,
                'PrimaCuota' => 1197.99,
                'Planid' => 3,
                'Plan' => 'Plan Básico ley',
                'Aseguradora' => $product['Vendor_Name']['name'],
                'Idcotizacion' => 3222373000214281001,
                'Fecha' => now()->toDateTimeString(),
                'CoberturasList' => null,
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
        $fields = ['id', 'Quoted_Items'];
        $quote = $this->crm->getRecords('Quotes', $fields, $id);

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];
        $this->crm->updateRecords('Quotes', $id, $data);

        return response()->noContent(200);
    }
}
