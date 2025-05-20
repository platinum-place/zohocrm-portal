<?php

namespace App\Http\Controllers\Quote;

use App\Http\Requests\Quote\EstimateUnemploymentRequest;
use App\Http\Requests\Quote\IssueLifeRequest;
use App\Services\Quote\QuoteUnemploymentService;

class QuoteUnemploymentController
{
    public function __construct(protected QuoteUnemploymentService $service)
    {
    }

    public function estimateUnemployment(EstimateUnemploymentRequest $request)
    {
        $products = $this->service->getLifeProducts('Desempleo');

        $response = [];

        foreach ($products as $product) {
            $response[] = [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'identificador' => '3222373000214281001',
                'Cliente' => 'Fulano de Tal',
                'Direccion' => 'Calle Primera',
                'Fecha' => '01/01/2020',
                'TipoEmpleado' => 'Privado',
                'IdentCliente' => '00030489834989',
                'Aseguradora ' => 'Mapfre',
                'MontoOriginal' => '000.00',
                'Cuota' => '000.00',
                'PlazoMese' => '24',
                'Total' => '000.00',
            ];
        }

        return response()->json($response);
    }

    public function issueUnemployment(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        $quote = $this->service->get($id)['data'][0];

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

            $this->service->update($id, $data);

            break;
        }

        return response()->noContent();
    }
}
