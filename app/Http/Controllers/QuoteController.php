<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quote\CancelVehicleRequest;
use App\Http\Requests\Quote\EstimateFireRequest;
use App\Http\Requests\Quote\EstimateLifeRequest;
use App\Http\Requests\Quote\EstimateUnemploymentDebtRequest;
use App\Http\Requests\Quote\EstimateUnemploymentRequest;
use App\Http\Requests\Quote\EstimateVehicleRequest;
use App\Http\Requests\Quote\InspectRequest;
use App\Http\Requests\Quote\IssueLifeRequest;
use App\Http\Requests\Quote\IssueVehicleRequest;
use App\Http\Requests\Quote\ValidateInspectionRequest;
use App\Services\QuoteService;
use App\Services\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class QuoteController extends Controller
{
    public function __construct(protected ZohoCRMService $crm)
    {
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function estimateVehicle(EstimateVehicleRequest $request)
    {
        $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Auto))';
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
                'Planid' => 11111,
                'Plan' => 'Plan Básico',
                'Aseguradora' => $product['Vendor_Name']['name'],
                'Idcotizacion' => 3222373000214282001,
                'Fecha' => now()->toDateTimeString(),
                'CoberturasList' => null,
            ];
        }

        return response()->json($response);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function issueVehicle(IssueVehicleRequest $request)
    {
        $id = $request->get('cotzid');

        $fields = ['id', 'Quoted_Items'];
        $quote = $this->crm->getRecords('Quotes', $fields, $id);

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

    public function valueVehicle()
    {
        return response()->json([
            'valorMinimo' => '0000',
            'valorEstandar' => '000.00',
            'valorMaximo' => '000.00',
        ]);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function validateInspection(ValidateInspectionRequest $request)
    {
        $id = $request->get('cotz_id');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404
            ], 404);
        }

        $data = [
            'Depurado' => true,
        ];
        $this->crm->updateRecords('Quotes', $id, $data);

        return response()->noContent(200);
    }

    /**
     * @throws \Exception
     * @throws Throwable
     */
    public function inspect(InspectRequest $request)
    {
        $id = $request->get('cotz_id');

        $photos = [
            'Foto1' => 'Foto Parte frontal',
            'Foto2' => 'Foto Parte trasera',
            'Foto3' => 'Foto Lateral Derecho',
            'Foto4' => 'FoFoto Interior Baul',
            'Foto5' => 'Foto Lateral Derecho',
            'Foto6' => 'Foto Chasis',
            'Foto7' => 'Foto Odometro',
            'Foto8' => 'Foto Interior',
            'Foto9' => 'Foto Motor',
            'Foto10' => 'Foto Repuesta',
            'Foto11' => 'Foto Interiooor2',
            'Foto12' => 'Foto Identificador Cliente',
            'Foto13' => 'Foto Matricula BL',
            'Foto14' => 'Otra foto',
        ];

        foreach ($photos as $photo => $title) {
            if (!$request->filled($photo)) {
                continue;
            }

            $imageData = base64_decode($request->input($photo));

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            $extension = match ($mimeType) {
                'image/jpeg' => 'jpg',
                'image/png' => 'png',
                default => throw new \Exception(__('validation.mimetypes', ['values' => '.jpg,.png']))
            };

            $path = "photos/{$id}/uploads/" . date('YmdHis') . "/$title.$extension";

            Storage::put($path, $imageData);

            $this->crm->uploadAnAttachment('Quotes', $id, $path);
        }

        return response()->noContent(200);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function getQRInspect(ValidateInspectionRequest $request)
    {
        $id = $request->get('cotz_id');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404
            ], 404);
        }

        $qr = base64_encode(QrCode::format('svg')
            ->size(80)
            ->generate("https://gruponobesrl.zcrmportals.com/portal/GrupoNobeSRL/crm/tab/Quotes/$id"));

        return response()->json([
            'QR' => $qr
        ]);
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     * @throws RequestException
     */
    public function getPhotos(ValidateInspectionRequest $request)
    {
        $id = $request->get('cotz_id');

        try {
            $fields = ['id', 'File_Name'];
            $attachments = $this->crm->attachmentList('Quotes', $id, $fields);
        } catch (Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404
            ], 404);
        }

        $response = [];

        foreach ($attachments['data'] as $attachment) {
            $imageData = $this->crm->getAttachment('Quotes', $id, $attachment['id']);

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_buffer($finfo, $imageData);
            finfo_close($finfo);

            $extension = match ($mimeType) {
                'image/jpeg' => '.jpg',
                'image/png' => '.png',
                default => throw new \Exception(__('validation.mimetypes', ['values' => '.jpg,.png']))
            };

            $path = "photos/{$id}/downloads/" . date('YmdHis') . "/{$attachment['File_Name']}.$extension";

            Storage::put($path, $imageData);

            $response[] = [$attachment['File_Name'] => base64_encode($imageData)];
        }

        return response()->json($response);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function estimateUnemploymentDebt(EstimateUnemploymentDebtRequest $request)
    {
        $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Vida/Desempleo))';
        $products = $this->crm->searchRecords('Products', $criteria);

        $response = [];

        foreach ($products['data'] as $product) {
            $response[] = [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'identificador' => '3222373000214281001',
                'Cliente' => $request->get('Cliente'),
                'Direccion' => $request->get('Direccion'),
                'TipoEmpleado' => 'Publico',
                'Fecha' => date('d/m/Y'),
                'IdenCliente' => $request->get('IdenCliente'),
                'Telefono' => $request->get('Telefono'),
                'Aseguradora' => $product['Vendor_Name']['name'],
                'MontoPrestamo' => '50000',
                'Cuota' => $request->get('Cuota'),
                'PlazoMese' => $request->get('Plazo') / 12,
                'Desempleo' => '6000',
                'Deuda' => '8000',
                'To tal' => '10000',
            ];
        }

        return response()->json($response);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function issueUnemploymentDebt(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404
            ], 404);
        }

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
     * @throws Throwable
     * @throws ConnectionException
     */
    public function estimateFire(EstimateFireRequest $request)
    {
        $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Incendio))';
        $products = $this->crm->searchRecords('Products', $criteria);

        $response = [];

        foreach ($products['data'] as $product) {
            $response[] = [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'PrimaVida' => '000.00',
                'PrimaTotalVida' => '000.00',
                'identificador' => '3222373000214281001',
                'Aseguradora' => $product['Vendor_Name']['name'],
                'Anios' => '5',
                'Valor' => '000.00',
                'EdadTerminar' => '35',
                'Codeudor' => 'Fulanito',
                'EdadCodeudor' => '30',
                'IdentiCodeudor' => '000000000',
                'CoberturasListInc' => null,
                'CoberturasListVid' => null,
            ];
        }

        return response()->json($response);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function issueFire(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404
            ], 404);
        }

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

    public function employmentTypes()
    {
        return response()->json([
            1 => 'Publico',
            2 => 'Privado',
        ]);
    }

    public function businessTypes()
    {
        return response()->json([
            1 => 'COMERCIAL',
            2 => 'CASA DE CAMPO',
        ]);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function cancelLife(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
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

    public function cancelFire(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
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

    public function cancelUnemployment(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
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

    public function cancelUnemploymentDebt(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
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

    public function cancelVehicle(CancelVehicleRequest $request)
    {
        $id = $request->get('IdCotizacion');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
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


    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function estimateLife(EstimateLifeRequest $request)
    {
        $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Vida))';
        $products = $this->crm->searchRecords('Products', $criteria);

        $response = [];

        foreach ($products['data'] as $product) {
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
                $criteria = "Plan:equals:" . $product['id'];
                $taxes = $this->crm->searchRecords('Tasas', $criteria);

                foreach ($taxes['data'] as $tax) {
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
                'Valid_Till' => date('Y-m-d', strtotime(date('Y-m-d') . '+ 30 days')),
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

            $responseProduct = $this->crm->insertRecords('Quotes', $data);

            $response[] = [
                'Impuesto' => null,
                'PrimaPeriodo' => null,
                'PrimaTotal' => $amount,
                'identificador' => $responseProduct['data'][0]['details']['id'],
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

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404
            ], 404);
        }

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
     * @throws Throwable
     * @throws ConnectionException
     */
    public function estimateUnemployment(EstimateUnemploymentRequest $request)
    {
        $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Desempleo))';
        $products = $this->crm->searchRecords('Products', $criteria);

        $response = [];

        foreach ($products['data'] as $product) {
            $response[] = [
                'Impuesto' => null,
                'PrimaPeriodo' => null,
                'PrimaTotal' => null,
                'identificador' => 3222373000214282001,
                'Cliente' => $request->get('Cliente'),
                'Direccion' => $request->get('Direccion'),
                'Fecha' => date('d/m/Y'),
                'TipoEmpleado' => null,
                'IdentCliente' => $request->get('IdentCliente'),
                'Aseguradora ' => $product['Vendor_Name']['name'],
                'MontoOriginal' => $request->get('MontoOriginal'),
                'Cuota' => $request->get('Cuota'),
                'PlazoMese' => $request->get('Plazo') * 12,
                'Total' => null,
            ];
        }

        return response()->json($response);
    }

    /**
     * @throws Throwable
     * @throws ConnectionException
     * @throws RequestException
     */
    public function issueUnemployment(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id);
        } catch (Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404
            ], 404);
        }
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
}
