<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

/**
 * OAuth
 */
$routes->post('oauth/token', 'OAuth::token');

/**
 * Auth
 */
$routes->get('login', 'Auth::index');
$routes->post('login', 'Auth::login');
$routes->put('logout', 'Auth::logout');
