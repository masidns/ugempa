<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->setAutoRoute(true);

$routes->get('/', 'Home::index');
$routes->get('/Testing', 'Testing::index');
$routes->get('/Dashboard', 'Dashboard::index');

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
    $routes->post('/calculateSilhouette', 'Clustering::calculateSilhouette');
});

$routes->group('/Silhouette', function ($routes) {
    $routes->get('/', 'Silhouette::index');
    $routes->post('/Silhouette/calculate', 'Silhouette::calculate');
    $routes->post('/Silhouette/result', 'Silhouette::result');
});
