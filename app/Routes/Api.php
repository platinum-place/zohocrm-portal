<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */


$routes->post('oauth/token', 'OAuth::token');

$routes->group('api', ['filter' => 'oauth2'], function ($routes) {
    /**
     * Quote
     */
    $routes->post('cotizador/colectiva', 'Api\Quote::colectiva');
    $routes->post('cotizador/EmitirAuto', 'Api\Quote::EmitirAuto');
    $routes->post('cotizador/CotizaVida', 'Api\Quote::CotizaVida');
    $routes->post('cotizador/EmitirVida', 'Api\Quote::EmitirVida');
    $routes->post('cotizador/CotizaDesempleoDeuda', 'Api\Quote::CotizaDesempleoDeuda');
    $routes->post('cotizador/CotizaDesempleo', 'Api\Quote::CotizaDesempleo');
    $routes->post('cotizador/EmitirDesempleoDeuda', 'Api\Quote::EmitirVida');
    $routes->post('cotizador/EmitirDesempleo', 'Api\Quote::EmitirVida');
    $routes->post('cotizador/CotizaIncendio', 'Api\Quote::CotizaIncendio');
    $routes->post('cotizador/EmitirIncendio', 'Api\Quote::EmitirVida');
    $routes->get('cotizador/ValorPromedio', 'Api\Quote::value');

    /**
     * Vehicle
     */
    $routes->post('vehiculos/Marca', 'Api\Vehicle::brands');
    $routes->post('vehiculos/Modelos/(:num)', 'Api\Vehicle::models/$1');
    $routes->post('vehiculos/TipoVehiculo', 'Api\Vehicle::types');

    /**
     * Service
     */
    $routes->get('Productos', 'Api\Service::index');
    $routes->get('Productos/Aseguradoras/(:num)', 'Api\Service::show/$1');
});