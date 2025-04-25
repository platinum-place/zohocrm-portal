<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

require APPPATH . 'Routes/Auth.php';


/**
 * --------------------------------------------------------------------
 * API
 * --------------------------------------------------------------------
 */
$routes->group('api', ['filter' => 'oauth2'], function ($routes) {
    /**
     * Quote
     */
    $routes->post('cotizador/colectiva', 'Api\Quote::estimateVehicle');
    $routes->post('cotizador/EmitirAuto', 'Api\Quote::issuePolicy');
    $routes->post('cotizador/CotizaVida', 'Api\Quote::estimateLife');
    $routes->post('cotizador/EmitirVida', 'Api\Quote::issueLife');
    $routes->post('cotizador/CotizaDesempleo', 'Api\Quote::estimateUnemployment');
    $routes->post('cotizador/EmitirDesempleo', 'Api\Quote::issueLife');
    $routes->post('cotizador/CotizaIncendio', 'Api\Quote::estimateFire');
    $routes->post('cotizador/EmitirIncendio', 'Api\Quote::issueLife');

    /**
     * Vehicle
     */
    $routes->post('vehiculos/Marca', 'Api\Vehicle::brands');
    $routes->post('vehiculos/Modelos/(:num)', 'Api\Vehicle::models/$1');
    $routes->post('vehiculos/TipoVehiculo', 'Api\Vehicle::types');

    /**
     * User
     */
    $routes->get('users', 'Api\User::index');


    /**
     * Vehicle
     */
    $routes->get('Productos', 'Api\Service::index');
    $routes->get('Productos/Aseguradoras/(:num)', 'Api\Service::show/$1');
});

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
