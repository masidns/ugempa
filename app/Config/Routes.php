<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);

$routes->get('/', 'Home::index');
$routes->get('/Testing', 'Testing::index');

$routes->group('Gempa', function ($routes) {
    $routes->get('/', 'Gempa::index');
    $routes->get('tambah', 'Gempa::tambah');
    $routes->get('CSV', 'Gempa::CSV');
    $routes->get('uploadCsv', 'Gempa::uploadCsv');
    $routes->post('save', 'Gempa::save');
    $routes->delete('delete/(:num)', 'Gempa::delete/$1');
});


$routes->group('/Clustering', function ($routes) {
    $routes->get('/', 'Clustering::index');
    $routes->post('/Clustering/cluster', 'Clustering::cluster');
});
