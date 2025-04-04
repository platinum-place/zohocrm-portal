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
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');

/**
 * OAuth
 */
$routes->post('oauth/token', 'OAuth::token');

/**
 * --------------------------------------------------------------------
 * API
 * --------------------------------------------------------------------
 */

/**
 * Quote
 */
$routes->post('api/cotizador/colectiva', 'Api\Quote::estimateVehicle');
$routes->post('api/cotizador/EmitirAuto', 'Api\Quote::issuePolicy');
$routes->post('api/cotizador/CotizaVida', 'Api\Quote::estimateLife');
$routes->post('api/cotizador/EmitirVida', 'Api\Quote::issueLife');
$routes->post('api/cotizador/CotizaDesempleo', 'Api\Quote::estimateUnemployment');
$routes->post('api/cotizador/EmitirDesempleo', 'Api\Quote::issueLife');
$routes->post('api/cotizador/CotizaIncendio', 'Api\Quote::estimateFire');
$routes->post('api/cotizador/EmitirIncendio', 'Api\Quote::issueLife');
$routes->get('api/Productos', 'Api\Quote::products');

/**
 * Vehicle
 */
$routes->post('api/vehiculos/Marca', 'Api\Vehicle::brands');
$routes->post('api/vehiculos/Modelos/(:num)', 'Api\Vehicle::models/$1');
$routes->post('api/vehiculos/TipoVehiculo', 'Api\Vehicle::types');


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
