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
    $routes->get('cotizador/ValorPromedio', 'Api\Quote::ValorPromedio');
    $routes->get('cotizador/GetTipoEmpleado', 'Api\Quote::GetTipoEmpleado');
    $routes->get('cotizador/GetGiroDelNegocio', 'Api\Quote::GetGiroDelNegocio');
    $routes->get('cotizador/CancelarVida', 'Api\Quote::CancelarVida');
    $routes->get('cotizador/CancelarIncendio', 'Api\Quote::CancelarVida');
    $routes->get('cotizador/CancelarDesempleo', 'Api\Quote::CancelarVida');
    $routes->get('cotizador/CancelarDesempleoDeuda', 'Api\Quote::CancelarVida');
    $routes->get('cotizador/CancelarAuto', 'Api\Quote::CancelarAuto');

    /**
     * Vehicle
     */
    $routes->post('vehiculos/Marca', 'Api\Vehicle::Marca');
    $routes->post('vehiculos/Modelos/(:num)', 'Api\Vehicle::Modelos/$1');
    $routes->post('vehiculos/TipoVehiculo', 'Api\Vehicle::TipoVehiculo');
    $routes->post('vehiculos/Accesorios', 'Api\Vehicle::Accesorios');
    $routes->post('vehiculos/Actividades', 'Api\Vehicle::Actividades');
    $routes->post('vehiculos/Circulacion', 'Api\Vehicle::Circulacion');
    $routes->get('vehiculos/Color', 'Api\Vehicle::Color');

    /**
     * Service
     */
    $routes->get('Productos', 'Api\Service::index');
    $routes->get('Productos/Aseguradoras/(:num)', 'Api\Service::show/$1');
});