<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('admin', ['filter' => 'auth.admin'], function ($routes) {
    $routes->get('users', 'User::index');
});
