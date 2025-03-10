<?php

namespace App\Libraries\Cotizar;

class CotizarVida extends Cotizar
{
    private $deudor = 0;
    private $codeudor = 0;

    private function verificar_comentarios($Plazo_max, $Suma_asegurada_min, $Suma_asegurada_max): string
    {
        if ($comentario = $this->limite_plazo($Plazo_max)) {
            return $comentario;
        }

        if ($comentario = $this->limite_suma($Suma_asegurada_min, $Suma_asegurada_max)) {
            return $comentario;
        }

        return "";
    }

    private function calcular_tasas($coberturaid)
    {
        //encontrar la tasa
        $criterio = "Plan:equals:$coberturaid";
        $tasas = $this->zoho->searchRecordsByCriteria("Tasas", $criterio);

        foreach ((array)$tasas as $tasa) {
            //verificar limite de edad
            if (
                $this->calcular_edad($this->cotizacion->fecha_deudor) >= $tasa->getFieldValue('Edad_min')
                and
                $this->calcular_edad($this->cotizacion->fecha_deudor) <= $tasa->getFieldValue('Edad_max')
            ) {
                $this->deudor = $tasa->getFieldValue('Name') / 100;
            }

            if (!empty($this->cotizacion->fecha_codeudor)) {
                if (
                    $this->calcular_edad($this->cotizacion->fecha_codeudor) >= $tasa->getFieldValue('Edad_min')
                    and
                    $this->calcular_edad($this->cotizacion->fecha_codeudor) <= $tasa->getFieldValue('Edad_max')
                ) {
                    $this->codeudor = $tasa->getFieldValue('Codeudor') / 100;
                }
            }
        }
    }

    private function calcular_prima($coberturaid)
    {
        //calcular tasas
        $this->calcular_tasas($coberturaid);

        $monto_prima = 0;
        //si existe codeudor
        if (!empty($this->cotizacion->fecha_codeudor)) {
            $prima_deudor = ($this->cotizacion->suma / 1000) * $this->deudor;
            $prima_codeudor = ($this->cotizacion->suma / 1000) * ($this->codeudor - $this->deudor);
            $monto_prima = $prima_deudor + $prima_codeudor;
        } else {
            $monto_prima = ($this->cotizacion->suma / 1000) * $this->deudor;
        }

        if ($monto_prima != 0) {
            $criterio = "Servicio:equals:" . $coberturaid;
            $mantenimientos = $this->zoho->searchRecordsByCriteria("Mantenimientos_cuentas", $criterio);
            $monto_mantenimiento = 0;
            foreach ((array)$mantenimientos as $mantenimiento) {
                if (
                    $mantenimiento->getFieldValue('Desde') <= $this->cotizacion->suma and
                    $mantenimiento->getFieldValue('Hasta') >= $this->cotizacion->suma
                ) {
                    $monto_mantenimiento = $mantenimiento->getFieldValue('Name');
                }
            }
            $this->cotizacion->monto_mantenimiento = $monto_mantenimiento;
        }

        return $monto_prima;
    }

    public function cotizar_planes()
    {
        //planes relacionados al banco
        $criterio = "((Corredor:equals:" . session("cuenta_id") . ") and (Product_Category:equals:Vida))";
        $coberturas = $this->zoho->searchRecordsByCriteria("Products", $criterio);

        foreach ((array)$coberturas as $cobertura) {
            //inicializacion de variables
            $prima = 0;

            //verificaciones
            $comentario = $this->verificar_comentarios(
                $cobertura->getFieldValue('Plazo_max'),
                $cobertura->getFieldValue('Suma_asegurada_min'),
                $cobertura->getFieldValue('Suma_asegurada_max')
            );

            //si no hubo un excepcion
            if (empty($comentario)) {
                $prima = $this->calcular_prima($cobertura->getEntityId());

                // en caso de haber algun problema
                if ($prima == 0) {
                    $comentario = "La edad del deudor o codeudor no estan dentro del limite permitido.";
                }
            }

            $neta = $prima * 0.16;
            $isc = $prima - ($prima * 0.16);

            //lista con los resultados de cada calculo
            $this->cotizacion->planes[] = [
                "aseguradora" => $cobertura->getFieldValue('Product_Name'),
                "planid" => $cobertura->getEntityId(),
                "prima" => $isc,
                "neta" => $neta,
                "total" => $prima,
                "suma" => $this->cotizacion->suma,
                "monto_mantenimiento" => $this->cotizacion->monto_mantenimiento,
                "comentario" => $comentario
            ];

            $this->deudor = 0;
            $this->codeudor = 0;
        }
    }
}
