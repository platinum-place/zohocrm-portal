<?php

namespace App\Http\Controllers;

use App\Http\Requests\Quote\CancelVehicleRequest;
use App\Http\Requests\Quote\DisableVehicleLawRequest;
use App\Http\Requests\Quote\EstimateFireRequest;
use App\Http\Requests\Quote\EstimateUnemploymentDebtRequest;
use App\Http\Requests\Quote\EstimateVehicleLawRequest;
use App\Http\Requests\Quote\EstimateVehicleRequest;
use App\Http\Requests\Quote\InspectRequest;
use App\Http\Requests\Quote\IssueLifeRequest;
use App\Http\Requests\Quote\IssueVehicleRequest;
use App\Http\Requests\Quote\SearchDocumentRequest;
use App\Http\Requests\Quote\ValidateInspectionRequest;
use App\Services\Quote\QuoteService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class QuoteController extends Controller
{
    public function __construct(protected QuoteService $service) {}

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
            if (! $request->filled($photo)) {
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

            $path = "photos/{$id}/uploads/".date('YmdHis')."/$title.$extension";

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

            $path = "photos/{$id}/downloads/".date('YmdHis')."/{$attachment['File_Name']}.$extension";

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

        $quote = $this->service->get($id)['data'][0];;

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

    public function estimateVehicleLaw(EstimateVehicleLawRequest $request)
    {
        return response()->json([
            [
                'Passcode' => '4821',
                'OfertaID' => 105,
                'Prima' => 12500.75,
                'Impuesto' => 1875.11,
                'PrimaTotal' => 14375.86,
                'PrimaCuota' => 1197.99,
                'Planid' => 3,
                'Plan' => 'Plan Básico',
                'Aseguradora' => 'Seguros XYZ',
                'Idcotizacion' => 3222373000214281001,
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
                'Planid' => 3,
                'Plan' => 'Plan Básico',
                'Aseguradora' => 'Seguros XYZ',
                'Idcotizacion' => 3222373000214281001,
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
                'Planid' => 3,
                'Plan' => 'Plan Básico',
                'Aseguradora' => 'Seguros XYZ',
                'Idcotizacion' => 3222373000214281001,
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
        ]);
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
     */
    public function searchDocument(SearchDocumentRequest $request, string $identification)
    {
        //        $search = $request->get('NoDocumento');

        $quotes = $this->service->searchQuote($identification);

        $response = [];

        foreach ($quotes as $quote) {
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
     */
    public function disableVehicleLaw(DisableVehicleLawRequest $request, string $id)
    {
        $this->service->get($id)['data'][0];

        $data = [
            'Quote_Stage' => 'Cancelada',
        ];

        $this->service->update($id, $data);

        return response()->noContent();
    }
}
