<?php

namespace App\Libraries\Cotizar;

use App\Libraries\Zoho;
use App\Models\Cotizacion;

class Cotizar
{
    protected $cotizacion;
    protected $zoho;

    public function __construct(Cotizacion $cotizacion, Zoho $zoho)
    {
        $this->cotizacion = $cotizacion;
        $this->zoho = $zoho;
    }

    protected function limite_suma($Suma_asegurada_min, $Suma_asegurada_max)
    {
	    if ($Suma_asegurada_min != null and $this->cotizacion->suma < $Suma_asegurada_min) {
	    	return "La suma asegurada es menor al limite."; 
	    }
		
		 if ($Suma_asegurada_max != null and $this->cotizacion->suma > $Suma_asegurada_max) {
	    	return "La suma asegurada es mayor al limite.";
	    }
		
        return "";
    }

    protected function limite_plazo($Plazo_max)
    {
        if ($this->cotizacion->plazo > $Plazo_max) {
            return "El plazo es mayor al limite establecido.";
        }

        return "";
    }

    protected function calcular_edad($fecha)
    {
        list ($ano, $mes, $dia) = explode("-", $fecha);
        $ano_diferencia = date("Y") - $ano;
        // $mes_diferencia = date("m") - $mes;
        // $dia_diferencia = date("d") - $dia;
        // if ($dia_diferencia < 0 || $mes_diferencia < 0)
        //     $ano_diferencia--;
        return $ano_diferencia;
    }
}