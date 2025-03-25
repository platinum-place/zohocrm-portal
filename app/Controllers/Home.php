<?php

namespace App\Controllers;


use App\Libraries\Emisiones;
use App\Libraries\Zoho;
use App\Libraries\ZohoClient;

class Home extends BaseController
{
    protected $zoho;

    public function __construct()
    {
        $this->zoho = new ZohoClient();
    }

    public function index()
    {
        $criterio = "Quote_Stage:starts_with:E";

        if (session('zoho_company_id')) {
            $criterio = "((Account_Name:equals:" . session('zoho_company_id') . ") and (Contact_Name:equals:" . session('zoho_id') . ") and ($criterio))";
        }

        $quotes_count = $this->zoho->recordCountInAModule('Quotes', $criterio);

        return view('home/index', [
            'quotes_count' => $quotes_count['count'],
            'quotes_list' => array(),
        ]);
    }

//    public function index(): string
//    {
//        $libreria = new \App\Libraries\Cotizaciones();
//        $emisiones = $libreria->lista_emisiones();
//
//        $lista = array();
//        $polizas = 0;
//
//        foreach ((array)$emisiones as $emision) {
//            //filtrar por  mes y año actual
//            if (date("Y-m", strtotime($emision->getFieldValue("Vigencia_desde"))) == date("Y-m")) {
//                $lista[] = $emision->getFieldValue('Coberturas')->getLookupLabel();
//                $polizas++;
//            }
//        }
//
//        return view('index', [
//            "titulo" => "Panel de Control",
//            "lista" => array_count_values($lista),
//            "polizas" => $polizas,
//            "cotizaciones" => $emisiones,
//        ]);
//    }
}
