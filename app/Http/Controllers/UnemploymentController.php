<?php

namespace App\Http\Controllers;

use App\Http\Requests\Unemployment\CancelUnemploymentRequest;
use App\Http\Requests\Unemployment\EstimateUnemploymentRequest;
use App\Http\Requests\Unemployment\IssueUnemploymentRequest;
use App\Services\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class UnemploymentController
{
    public function __construct(protected ZohoCRMService $crm)
    {
    }

    /**
     * @throws ConnectionException
     * @throws RequestException
     */
    public function estimateUnemployment(EstimateUnemploymentRequest $request)
    {
        $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Desempleo))';
        $response = $this->crm->searchRecords('Products', $criteria);

        $quotes = [];

        foreach ($response['data'] as $product) {
            $alert = '';

            if ($request->get('PlazoDias') > $product['Plazo_max']) {
                $alert = 'El plazo es mayor al limite establecido.';
            }

            if ($request->get('MontoOriginal') < $product['Suma_asegurada_min'] || $request->get('MontoOriginal') > $product['Suma_asegurada_max']) {
                $alert = 'El plazo es mayor al limite establecido.';
            }

            $unemploymentTax = 0;
            $lifeTax = 0;
            $amount = 0;

            try {
                $criteria = 'Plan:equals:' . $product['id'];
                $taxes = $this->crm->searchRecords('Tasas', $criteria);

                foreach ($taxes['data'] as $tax) {
                    if ($request->get('TiempoLaborando') >= $tax['Edad_min'] and $request->get('TiempoLaborando') <= $tax['Edad_max']) {
                        $lifeTax = $tax['Name'] / 100;
                        $unemploymentTax = $tax['Desempleo'];
                    } else {
                        $alert = 'La edad del deudor no estan dentro del limite permitido.';
                    }
                }
            } catch (Throwable $throwable) {

            }

            if (empty($alert)) {
                $lifeAmount = ($request->get('MontoOriginal') / 1000) * $lifeTax;
                $unemploymentAmount = ($request->get('MontoOriginal') / 1000) * $unemploymentTax;
                $amount = round($lifeAmount + $unemploymentAmount, 2);
            }

            $data = [
                "Subject" => $request->get('Cliente'),
                "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 30 days")),
                "Vigencia_desde" => date("Y-m-d"),
                'Account_Name' => 3222373000092390001,
                'Contact_Name' => 3222373000203318001,
                "Quote_Stage" => "Cotizando",
                "Nombre" => $request->get('Cliente'),
                "RNC_C_dula" => $request->get('IdentCliente'),
                "Direcci_n" => $request->get('Direccion'),
                "Tel_Celular" => $request->get('Telefono'),
                "Plan" => 'Vida/Desempleo',
                "Suma_asegurada" => (float)$request->get('MontoOriginal'),
                "Plazo" => $request->get('Plazo') * 12,
                "Cuota" => $request->get('Cuota'),
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

            $responseQuote = $this->crm->insertRecords('Quotes', $data);

            $quotes[] = [
                'Impuesto' => round($amount * 0.16, 2),
                'identificador' => number_to_uuid($responseQuote['data'][0]['details']['id']),
                'Cliente' => $request->get('Cliente'),
                'Direccion' => $request->get('Direccion'),
                'Fecha' => now()->format('Y-m-d\TH:i:sP'),
                'TipoEmpleado' => $request->get('idTipoEmpleado'),
                'IdentCliente' => $request->get('IdentCliente'),
                'Aseguradora' => $product['Vendor_Name']['name'],
                'MontoOriginal' => (float)sprintf('%.1f', $request->get('MontoOriginal')),
                'Cuota' => (float)sprintf('%.1f', $request->get('Cuota')),
                'PlazoMeses' => $request->get('Plazo') * 12,
                'Prima' => $amount,
                'Alerta' => $alert,
                'Error' => null,
            ];
        }

        return response()->json($quotes);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function issueUnemployment(IssueUnemploymentRequest $request)
    {
        $id = uuid_to_number($request->get('Identificador'));

        $fields = ['id', 'Quoted_Items'];
        $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];

        foreach ($quote['Quoted_Items'] as $line) {
            $data = [
                'Coberturas' => $line['Product_Name']['id'],
                'Quote_Stage' => 'Emitida',
                'Vigencia_desde' => date('Y-m-d'),
                'Valid_Till' => date('Y-m-d', strtotime(date('Y-m-d') . '+ 1 years')),
                'Prima_neta' => round($line['Net_Total'] / 1.16, 2),
                'ISC' => round($line['Net_Total'] - ($line['Net_Total'] / 1.16), 2),
                'Prima' => round($line['Net_Total'], 2),
            ];

            $this->crm->updateRecords('Quotes', $id, $data);

            break;
        }

        return response()->noContent(200);
    }

    /**
     * @throws RequestException
     * @throws ConnectionException
     */
    public function cancelUnemployment(CancelUnemploymentRequest $request)
    {
        $id = uuid_to_number($request->get('Identificador'));

        $fields = ['id', 'Plan', 'Quoted_Items'];
        $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];

        if ($quote['Plan'] != 'Vida/Desempleo') {
            throw new NotFoundHttpException(__('Not Found'));
        }

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];

        $this->crm->updateRecords('Quotes', $id, $data);

        return response()->noContent(200);
    }
}
