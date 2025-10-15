<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'login::index');

$routes->setAutoRoute(true);
$routes->post('InventoryController/updateOrderStatus/(:num)', 'InventoryController::updateOrderStatus/$1');