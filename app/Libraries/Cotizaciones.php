<?php

namespace App\Libraries;

use zcrmsdk\crm\exception\ZCRMException;

class Cotizaciones extends Zoho
{
    public function emisiones($plan_criterio)
    {
        $cuenta_criterio = "(Account_Name:equals:" . session('cuenta_id') . ")";
        $estado_criterio = "(Quote_Stage:starts_with:E)";

        if (session('admin') == false) {
            $user_criterio = "(Account_Name:equals:" . session('cuenta_id') . ")";
            $criterio = "($cuenta_criterio and $estado_criterio and $plan_criterio and $user_criterio)";
        } else {
            $criterio = "($cuenta_criterio and $estado_criterio and $plan_criterio)";
        }

        return $this->searchRecordsByCriteria("Quotes", $criterio);
        // $pag = 1;

        // do {
        //     if ($array = $this->searchRecordsByCriteria("Quotes", $criterio, $pag)) {
        //         dd($array);
        //         $emisiones = array_merge($array, $emisiones);
        //         $pag++;
        //     } else {
        //         $pag = 0;
        //     }
        // } while ($pag == 0);

        // return $emisiones;
    }

    public function lista_cotizaciones(): ?array
    {
        if (session('admin') == true) {
            $criterio = "((Account_Name:equals:" . session('cuenta_id') . ") and (Quote_Stage:starts_with:C))";
        } else {
            $criterio = "((Account_Name:equals:" . session('cuenta_id') . ") and (Contact_Name:equals:" . session('usuario_id') . ") and (Quote_Stage:starts_with:C))";
        }

        return $this->searchRecordsByCriteria("Quotes", $criterio);
    }

    public function lista_emisiones(): ?array
    {
        if (session('admin') == true) {
            $criterio = "((Account_Name:equals:" . session('cuenta_id') . ") and (Quote_Stage:starts_with:E))";
        } else {
            $criterio = "((Account_Name:equals:" . session('cuenta_id') . ") and (Contact_Name:equals:" . session('usuario_id') . ") and (Quote_Stage:starts_with:E))";
        }

        return $this->searchRecordsByCriteria("Quotes", $criterio);
    }

    /**
     * @throws ZCRMException
     */
    public function actualizar_cotizacion($cotizacion, $planid)
    {
        // obtener los datos del plan elegido
        foreach ($cotizacion->getLineItems() as $lineItem) {
            if ($planid == $lineItem->getProduct()->getEntityId()) {
                $cambios = [
                    "Coberturas" => $planid,
                    "Quote_Stage" => "Emitida",
                    "Vigencia_desde" => date("Y-m-d"),
                    "Valid_Till" => date("Y-m-d", strtotime(date("Y-m-d") . "+ 1 years")),
                ];

                $cambios['Prima_neta'] = round($lineItem->getNetTotal() / 1.16, 2);
                $cambios['ISC'] = round($lineItem->getNetTotal() - $lineItem->getNetTotal() / 1.16, 2);
                $cambios['Prima'] = round($lineItem->getNetTotal(), 2);

                if (session()->get('cuenta_id') == "3222373000005967119") {
                    $data = json_decode($lineItem->getDescription(), true);
                    $cambios['Monto_mantenimiento'] = round($data['monto_mantenimiento'], 2);
                }

                $this->update("Quotes", $cotizacion->getEntityId(), $cambios);
            }
        }
    }

    public function adjuntar_archivo($documentos, $id)
    {
        foreach ($documentos as $documento) {
            if ($documento->isValid() && !$documento->hasMoved()) {
                // subir el archivo al servidor
                $documento->move(WRITEPATH . 'uploads');
                // ruta del archivo subido
                $ruta = WRITEPATH . 'uploads/' . $documento->getClientName();
                // funcion para adjuntar el archivo
                $this->uploadAttachment("Quotes", $id, $ruta);
                // borrar el archivo del servidor local
                unlink($ruta);
            }
        }
    }
}
