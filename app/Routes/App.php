<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('admin/login', 'Auth::index');
$routes->post('admin/login', 'Auth::login');
$routes->put('admin/logout', 'Auth::logout');

$routes->group('' /** , ['filter' => 'auth'] */, function ($routes) {
    $routes->get('/', 'Home::index');
});

$routes->group('admin', ['filter' => 'auth.admin'], function ($routes) {
    /**
     * User
     */
    $routes->get('users', 'User::index');
    $routes->get('users/create', 'User::create');
    $routes->post('users/store', 'User::store');
    $routes->get('users/edit/(:segment)', 'User::edit/$1');
    $routes->put('users/update/(:segment)', 'User::update/$1');
    $routes->get('users/reset-password/(:segment)', 'User::resetPassword/$1');
    $routes->delete('users/delete/(:segment)', 'User::delete/$1');

    /**
     * Client
     */
    $routes->get('clients', 'Client::index');
    $routes->get('clients/edit/(:segment)', 'Client::edit/$1');
    $routes->put('clients/update/(:segment)', 'Client::update/$1');
});