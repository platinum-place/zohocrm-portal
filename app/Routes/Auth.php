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
$routes->get('login', 'Auth::login');
