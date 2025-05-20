<?php

namespace App\Http\Controllers\Quote;

use App\Http\Requests\Quote\EstimateLifeRequest;
use App\Http\Requests\Quote\IssueLifeRequest;
use App\Services\Quote\QuoteLifeService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Throwable;

class QuoteLifeController
{
    public function __construct(protected QuoteLifeService $service) {}

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function estimateLife(EstimateLifeRequest $request)
    {
        $products = $this->service->getLifeProducts('Vida');

        $response = [];

        foreach ($products as $product) {
            $alert = '';

            if ($request->get('PlazoDias') > $product['Plazo_max']) {
                $alert = 'El plazo es mayor al limite establecido.';
            }

            if ($request->get('MontoOriginal') < $product['Suma_asegurada_min'] || $request->get('MontoOriginal') > $product['Suma_asegurada_max']) {
                $alert = 'El plazo es mayor al limite establecido.';
            }

            $debtTax = 0;
            $coDebtTax = 0;
            $amount = 0;

            try {
                $taxes = $this->service->getProductTaxes($product['id']);

                foreach ($taxes as $tax) {
                    if ($request->get('Edad') >= $tax['Edad_min'] and $request->get('Edad') <= $tax['Edad_max']) {
                        $debtTax = $tax['Name'] / 100;
                    } else {
                        $alert = 'La edad del deudor no estan dentro del limite permitido.';
                    }

                    if ($request->get('codeudor') and $request->get('EdadCodeudor')) {
                        if ($request->get('EdadCodeudor') >= $tax['Edad_min'] and $request->get('EdadCodeudor') <= $tax['Edad_max']) {
                            $coDebtTax = $tax['Codeudor'] / 100;
                        } else {
                            $alert = 'La edad del codeudor no estan dentro del limite permitido.';
                        }
                    }
                }
            } catch (Throwable $throwable) {

            }

            if (empty($alert)) {
                if ($debtTax && $coDebtTax) {
                    $amountDebt = ($request->get('MontoOriginal') / 1000) * $debtTax;
                    $amountCoDebt = ($request->get('MontoOriginal') / 1000) * ($coDebtTax - $debtTax);
                    $amount = $amountDebt + $amountCoDebt;
                } elseif ($debtTax) {
                    $amount = ($request->get('MontoOriginal') / 1000) * $debtTax;
                }

                $amount = round($amount, 2);
            }

            $data = [
                'Subject' => $request->get('NombreCliente'),
                'Valid_Till' => date('Y-m-d', strtotime(date('Y-m-d').'+ 30 days')),
                'Vigencia_desde' => date('Y-m-d'),
                'Account_Name' => 3222373000092390001,
                'Contact_Name' => 3222373000203318001,
                'Quote_Stage' => 'Cotizando',
                'Nombre' => $request->get('NombreCliente'),
                'RNC_C_dula' => $request->get('IdenCliente'),
                'Direcci_n' => $request->get('Direccion'),
                'Tel_Celular' => $request->get('Telefono1'),
                'Plan' => 'Vida',
                'Suma_asegurada' => $request->get('MontoOriginal'),
                'Plazo' => $request->get('PlazoAnios'),
                'Fuente' => 'API',
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

            $responseProduct = $this->service->create($data);

            $response[] = [
                'Impuesto' => null,
                'PrimaPeriodo' => null,
                'PrimaTotal' => $amount,
                'identificador' => $responseProduct['details']['id'],
                'Aseguradora' => $product['Vendor_Name']['name'],
                'MontoOrig' => $request->get('MontoOriginal'),
                'Anios' => null,
                'EdadTerminar' => null,
                'Cliente' => $request->get('NombreCliente'),
                'Fecha' => date('d/m/Y'),
                'Direccion' => $request->get('Direccion'),
                'Edad' => $request->get('Edad'),
                'IdenCliente' => $request->get('IdenCliente'),
                'Codeudor' => null,
                'Alerta' => $alert,
            ];
        }

        return response()->json($response);
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     * @throws RequestException
     */
    public function issueLife(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        $quote = $this->service->get($id)['data'][0];

        foreach ($quote['Quoted_Items'] as $line) {
            $data = [
                'Coberturas' => $line['Product_Name']['id'],
                'Quote_Stage' => 'Emitida',
                'Vigencia_desde' => date('Y-m-d'),
                'Valid_Till' => date('Y-m-d', strtotime(date('Y-m-d').'+ 1 years')),
                'Prima_neta' => round($line['Net_Total'] / 1.16, 2),
                'ISC' => round($line['Net_Total'] - ($line['Net_Total'] / 1.16), 2),
                'Prima' => round($line['Net_Total'], 2),
            ];

            $this->service->update($id, $data);

            break;
        }

        return response()->noContent();
    }
}
