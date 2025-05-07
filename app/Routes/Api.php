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
    $routes->post('cotizador/colectiva', 'Api\Quote::estimateVehicle');
    $routes->post('cotizador/EmitirAuto', 'Api\Quote::issuePolicy');
    $routes->post('cotizador/CotizaVida', 'Api\Quote::estimateLife');
    $routes->post('cotizador/EmitirVida', 'Api\Quote::issueLife');
    $routes->post('cotizador/CotizaDesempleoDeuda', 'Api\Quote::estimateUnemployment');
    $routes->post('cotizador/EmitirDesempleoDeuda', 'Api\Quote::issueLife');
    $routes->post('cotizador/CotizaIncendio', 'Api\Quote::estimateFire');
    $routes->post('cotizador/EmitirIncendio', 'Api\Quote::issueLife');

    /**
     * Vehicle
     */
    $routes->post('vehiculos/Marca', 'Api\Vehicle::brands');
    $routes->post('vehiculos/Modelos/(:num)', 'Api\Vehicle::models/$1');
    $routes->post('vehiculos/TipoVehiculo', 'Api\Vehicle::types');

    /**
     * Vehicle
     */
    $routes->get('Productos', 'Api\Service::index');
    $routes->get('Productos/Aseguradoras/(:num)', 'Api\Service::show/$1');
});