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
use App\Services\ZohoCRMService;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Throwable;

class QuoteController extends Controller
{
    public function __construct(protected ZohoCRMService $crm) {}

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function estimateVehicle(EstimateVehicleRequest $request)
    {
        try {
            $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Auto))';
            $products = $this->crm->searchRecords('Products', $criteria);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

        $response = [];

        foreach ($products['data'] as $product) {
            $alert = '';

            if (in_array($request->get('Actividad'), $product['Restringir_veh_culos_de_uso'])) {
                return 'Uso del vehículo restringido.';
            }

            if ((date('Y') - $request->get('Anio')) > $product['Max_antig_edad']) {
                $alert = 'La antigüedad del vehículo es mayor al limite establecido.';
            }

            if ($request->get('MontoOriginal') < $product['Suma_asegurada_min'] || $request->get('MontoOriginal') > $product['Suma_asegurada_max']) {
                $alert = 'El plazo es mayor al limite establecido.';
            }

            try {
                $criteria = '((Marca:equals:'.$request->get('Marca').') and (Aseguradora:equals:'.$product['Vendor_Name']['id'].'))';
                $brands = $this->crm->searchRecords('Restringidos', $criteria);

                foreach ($brands['data'] as $brand) {
                    if (empty($brand['Modelo'])) {
                        $alert = 'Marca restrigida.';
                    } elseif ($request->get('Marca') == $brand['Modelo']['id']) {
                        $alert = 'Modelo restrigido.';
                    }
                }
            } catch (Throwable $throwable) {

            }

            $taxAmount = 0;

            try {
                $criteria = 'Plan:equals:'.$product['id'];
                $taxes = $this->crm->searchRecords('Tasas', $criteria);

                foreach ($taxes['data'] as $tax) {
                    if (in_array($request->get('TipoVehiculo'), $tax['Grupo_de_veh_culo'])) {
                        if (! empty($tax['Suma_limite'])) {
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

            if (! $taxAmount) {
                $alert = 'No se encontraron tasas.';
            }

            $surchargeAmount = 0;

            try {
                $criteria = '((Marca:equals:'.$request->get('Marca').') and (Aseguradora:equals:'.$product['Vendor_Name']['id'].'))';
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
                'Subject' => $request->get('NombreCliente'),
                'Valid_Till' => date('Y-m-d', strtotime(date('Y-m-d').'+ 30 days')),
                'Vigencia_desde' => date('Y-m-d'),
                'Account_Name' => 3222373000092390001,
                'Contact_Name' => 3222373000203318001,
                'Quote_Stage' => 'Cotizando',
                'Nombre' => $request->get('NombreCliente'),
                'Fecha_de_nacimiento' => $request->get('FechaNacimiento'),
                'RNC_C_dula' => $request->get('IdCliente'),
                'Correo_electr_nico' => $request->get('Email'),
                'Tel_Celular' => $request->get('TelefMovil'),
                'Tel_Residencia' => $request->get('TelefResidencia'),
                'Tel_Trabajo' => $request->get('TelefTrabajo'),
                'Plan' => 'Mensual Full',
                'Suma_asegurada' => $request->get('MontoAsegurado'),
                'A_o' => $request->get('Anio'),
                'Marca' => $request->get('Marca'),
                'Modelo' => $request->get('Modelo'),
                'Tipo_veh_culo' => $request->get('TipoVehiculo'),
                'Chasis' => $request->get('Chasis'),
                'Placa' => $request->get('Placa'),
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

    /**
     * @throws RequestException
     * @throws Throwable
     * @throws ConnectionException
     */
    public function issueVehicle(IssueVehicleRequest $request)
    {
        $id = $request->get('cotzid');

        try {
            $fields = ['id', 'Quoted_Items'];
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (Throwable $exception) {
            return response([
                'Error' => $exception->getMessage(),
                'code' => 404,
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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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

        try {
            $fields = ['id', 'File_Name'];
            $attachments = $this->crm->attachmentList('Quotes', $id, $fields);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
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
        try {
            $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Desempleo))';
            $products = $this->crm->searchRecords('Products', $criteria);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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
                'PlazoMese' => $request->get('Plazo') * 12,
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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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
        try {
            $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Incendio))';
            $products = $this->crm->searchRecords('Products', $criteria);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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

            $this->crm->updateRecords('Quotes', $id, $data);

            break;
        }

        return response()->noContent(200);
    }

    public function employmentTypes()
    {
        $types = [
            [
                'IdTipoEmpleado' => 3,
                'TipoEmpleado' => 'Independiente',
            ],
            [
                'IdTipoEmpleado' => 1,
                'TipoEmpleado' => 'Privado',
            ],
            [
                'IdTipoEmpleado' => 2,
                'TipoEmpleado' => 'Publico',
            ],
        ];

        return response()->json($types);
    }

    public function businessTypes()
    {
        $types = [
            [
                'IdGiroDelNegocio' => 1,
                'GiroDelNegocio' => 'COMERCIOS VARIOS',
            ],
            [
                'IdGiroDelNegocio' => 10,
                'GiroDelNegocio' => 'CONSULTORIOS MEDICOS',
            ],
            [
                'IdGiroDelNegocio' => 2,
                'GiroDelNegocio' => 'HOSPITALES/CLINICAS',
            ],
            [
                'IdGiroDelNegocio' => 9,
                'GiroDelNegocio' => 'HOTELES DE CIUDAD',
            ],
            [
                'IdGiroDelNegocio' => 12,
                'GiroDelNegocio' => 'Local comercial',
            ],
            [
                'IdGiroDelNegocio' => 4,
                'GiroDelNegocio' => 'OTROS',
            ],
        ];

        return response()->json($types);
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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
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
        try {
            $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Vida))';
            $products = $this->crm->searchRecords('Products', $criteria);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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
                $criteria = 'Plan:equals:'.$product['id'];
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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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
        try {
            $criteria = '((Corredor:equals:3222373000092390001) and (Product_Category:equals:Desempleo))';
            $products = $this->crm->searchRecords('Products', $criteria);
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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
            $quote = $this->crm->getRecords('Quotes', $fields, $id)['data'][0];
        } catch (\Exception $e) {
            return response()->json(['Error' => $e->getMessage()], 404);
        }

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

            $this->crm->updateRecords('Quotes', $id, $data);

            break;
        }

        return response()->noContent(200);
    }
}
