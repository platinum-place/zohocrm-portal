<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quote\CancelVehicleRequest;
use App\Http\Requests\Quote\EstimateFireRequest;
use App\Http\Requests\Quote\EstimateUnemploymentDebtRequest;
use App\Http\Requests\Quote\EstimateVehicleRequest;
use App\Http\Requests\Quote\InspectRequest;
use App\Http\Requests\Quote\IssueLifeRequest;
use App\Http\Requests\Quote\IssueVehicleRequest;
use App\Http\Requests\Quote\ValidateInspectionRequest;
use App\Services\QuoteService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class QuoteController extends Controller
{
    public function __construct(protected QuoteService $service)
    {
    }

    public function estimateVehicle(EstimateVehicleRequest $request)
    {
        return response()->json([
            [
                'Passcode' => '4821',
                'OfertaID' => 105,
                'Prima' => 12500.75,
                'Impuesto' => 1875.11,
                'PrimaTotal' => 14375.86,
                'PrimaCuota' => 1197.99,
                'Planid' => 3222373000214282001,
                'Plan' => 'Plan Básico',
                'Aseguradora' => 'Seguros XYZ',
                'Idcotizacion' => 789654,
                'Fecha' => now()->toDateTimeString(),
                'CoberturasList' => [
                    [
                        'id' => 1,
                        'nombre' => 'Cobertura Total',
                        'descripcion' => 'Cobertura completa del vehículo',
                    ],
                    [
                        'id' => 2,
                        'nombre' => 'Cobertura Total',
                        'descripcion' => 'Cobertura completa del vehículo',
                    ],
                    [
                        'id' => 3,
                        'nombre' => 'Cobertura Total',
                        'descripcion' => 'Cobertura completa del vehículo',
                    ],
                ],
            ],
            [
                'Passcode' => '4821',
                'OfertaID' => 105,
                'Prima' => 12500.75,
                'Impuesto' => 1875.11,
                'PrimaTotal' => 14375.86,
                'PrimaCuota' => 1197.99,
                'Planid' => 3222373000214281014,
                'Plan' => 'Plan Básico',
                'Aseguradora' => 'Seguros XYZ',
                'Idcotizacion' => 789654,
                'Fecha' => now()->toDateTimeString(),
                'CoberturasList' => null,
            ],
            [
                'Passcode' => '4821',
                'OfertaID' => 105,
                'Prima' => 12500.75,
                'Impuesto' => 1875.11,
                'PrimaTotal' => 14375.86,
                'PrimaCuota' => 1197.99,
                'Planid' => 3222373000214281001,
                'Plan' => 'Plan Básico',
                'Aseguradora' => 'Seguros XYZ',
                'Idcotizacion' => 789654,
                'Fecha' => now()->toDateTimeString(),
                'CoberturasList' => null,
            ],
        ]);
    }

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function issueVehicle(IssueVehicleRequest $request)
    {
        $id = $request->get('cotzid');

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

        $this->service->get($id);

        $data = [
            'Depurado' => true,
        ];

        $this->service->update($id, $data);

        return response()->noContent();
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
            $this->service->uploadAttachment($id, $path);
        }

        return response()->noContent();
    }

    public function getQRInspect(ValidateInspectionRequest $request)
    {
        $id = $request->get('cotz_id');

        $this->service->get($id);

        $qr = base64_encode(QrCode::format('svg')
            ->size(80)
            ->generate("https://gruponobesrl.zcrmportals.com/portal/GrupoNobeSRL/crm/tab/Quotes/$id"));

        return response()->json([
            'QR' => $qr,
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

        $attachments = $this->service->getAttachments($id);

        $response = [];

        foreach ($attachments as $attachment) {
            $imageData = $this->service->downloadAttachment($id, $attachment['id']);

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
        return response()->json([
            [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'identificador' => '3222373000214281001',
                'Cliente' => 'Fulano del tal',
                'Direccion' => 'Calle Primera',
                'TipoEmpleado' => 'Publico',
                'Fecha' => '01/01/2020',
                'IdenCliente' => '000101001002030',
                'Telefono' => '809390903',
                'Aseguradora' => 'Mapfre',
                'MontoPrestamo' => '50000',
                'Cuota' => '5000',
                'PlazoMese' => '24',
                'Desempleo' => '6000',
                'Deuda' => '8000',
                'To tal' => '10000',
            ],
            [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'identificador' => '3222373000214281001',
                'Cliente' => 'Fulano del tal',
                'Direccion' => 'Calle Primera',
                'TipoEmpleado' => 'Publico',
                'Fecha' => '01/01/2020',
                'IdenCliente' => '000101001002030',
                'Telefono' => '809390903',
                'Aseguradora' => 'Mapfre',
                'MontoPrestamo' => '50000',
                'Cuota' => '5000',
                'PlazoMese' => '24',
                'Desempleo' => '6000',
                'Deuda' => '8000',
                'To tal' => '10000',
            ],
            [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'identificador' => '3222373000214281001',
                'Cliente' => 'Fulano del tal',
                'Direccion' => 'Calle Primera',
                'TipoEmpleado' => 'Publico',
                'Fecha' => '01/01/2020',
                'IdenCliente' => '000101001002030',
                'Telefono' => '809390903',
                'Aseguradora' => 'Mapfre',
                'MontoPrestamo' => '50000',
                'Cuota' => '5000',
                'PlazoMese' => '24',
                'Desempleo' => '6000',
                'Deuda' => '8000',
                'To tal' => '10000',
            ],
        ]);
    }

    public function issueUnemploymentDebt(IssueLifeRequest $request)
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

    public function estimateFire(EstimateFireRequest $request)
    {
        return response()->json([
            [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'PrimaVida' => '000.00',
                'PrimaTotalVida' => '000.00',
                'identificador' => '3222373000214281001',
                'Aseguradora' => 'Mapfre',
                'Anios' => '5',
                'Valor' => '000.00',
                'EdadTerminar' => '35',
                'Codeudor' => 'Fulanito',
                'EdadCodeudor' => '30',
                'IdentiCodeudor' => '000000000',
                'CoberturasListInc' => [
                    'Cobertura' => 'Incendio',
                    'Valor' => '100%',
                ],
                'CoberturasListVid' => [
                    'Cobertura' => 'Vida',
                    'Valor' => '100%',
                ],
            ],
            [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'PrimaVida' => '000.00',
                'PrimaTotalVida' => '000.00',
                'identificador' => '3222373000214281001',
                'Aseguradora' => 'Mapfre',
                'Anios' => '5',
                'Valor' => '000.00',
                'EdadTerminar' => '35',
                'Codeudor' => 'Fulanito',
                'EdadCodeudor' => '30',
                'IdentiCodeudor' => '000000000',
                'CoberturasListInc' => [
                    'Cobertura' => 'Incendio',
                    'Valor' => '100%',
                ],
                'CoberturasListVid' => [
                    'Cobertura' => 'Vida',
                    'Valor' => '100%',
                ],
            ],
            [
                'Impuesto' => '18.5',
                'PrimaPeriodo' => '000.00',
                'PrimaTotal' => '000.00',
                'PrimaVida' => '000.00',
                'PrimaTotalVida' => '000.00',
                'identificador' => '3222373000214281001',
                'Aseguradora' => 'Mapfre',
                'Anios' => '5',
                'Valor' => '000.00',
                'EdadTerminar' => '35',
                'Codeudor' => 'Fulanito',
                'EdadCodeudor' => '30',
                'IdentiCodeudor' => '000000000',
                'CoberturasListInc' => [
                    'Cobertura' => 'Incendio',
                    'Valor' => '100%',
                ],
                'CoberturasListVid' => [
                    'Cobertura' => 'Vida',
                    'Valor' => '100%',
                ],
            ],
        ]);
    }

    public function issueFire(IssueLifeRequest $request)
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

        $this->service->get($id)['data'][0];

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];

        $this->service->update($id, $data);

        return response()->noContent();
    }

    public function cancelFire(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        $this->service->get($id)['data'][0];

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];

        $this->service->update($id, $data);

        return response()->noContent();
    }

    public function cancelUnemployment(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        $this->service->get($id)['data'][0];

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];

        $this->service->update($id, $data);

        return response()->noContent();
    }

    public function cancelUnemploymentDebt(IssueLifeRequest $request)
    {
        $id = $request->get('Identificador');

        $this->service->get($id)['data'][0];

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];

        $this->service->update($id, $data);

        return response()->noContent();
    }

    public function cancelVehicle(CancelVehicleRequest $request)
    {
        $id = $request->get('IdCotizacion');

        $this->service->get($id)['data'][0];

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];

        $this->service->update($id, $data);

        return response()->noContent();
    }


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
