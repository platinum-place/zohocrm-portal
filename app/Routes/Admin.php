<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->group('admin', ['filter' => 'auth.admin'], function ($routes) {
    /**
     * User
     */
    $routes->get('users', 'User::index');
    $routes->get('users/edit/(:segment)', 'User::edit/$1');
    $routes->put('users/update/(:segment)', 'User::update/$1');
    $routes->get('users/reset-password/(:segment)', 'User::resetPassword/$1');
    $routes->delete('users/delete/(:segment)', 'User::delete/$1');
});
