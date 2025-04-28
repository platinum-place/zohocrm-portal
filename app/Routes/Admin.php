<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('admin', ['filter' => 'auth.admin'], function ($routes) {
    $routes->get('users', 'User::index');
    $routes->get('users/edit/(:segment)', 'User::edit/$1');
    $routes->put('users/update/(:segment)', 'User::update/$1');
});
