<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

/*
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('DashboardController');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
// The Auto Routing (Legacy) is very dangerous. It is easy to create vulnerable apps
// where controller filters or CSRF protection are bypassed.
// If you don't want to define all routes, please use the Auto Routing (Improved).
// Set `$autoRoutesImproved` to true in `app/Config/Feature.php` and set the following to true.
// $routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

$routes->get('/login', 'Auth\AuthenticatedSessionController::create', ['filter' => 'auth:guest']);
$routes->post('/login', 'Auth\AuthenticatedSessionController::store', ['filter' => 'auth:guest']);
$routes->delete('/logout', 'Auth\AuthenticatedSessionController::destroy');

// Dashboard
$routes->get('/', 'DashboardController::index');

// Users
$routes->get('/users', 'UsersController::index');
$routes->get('/users/create', 'UsersController::create');
$routes->post('/users', 'UsersController::store');
$routes->get('/users/(:num)/edit', 'UsersController::edit/$1');
$routes->post('/users/(:num)', 'UsersController::update/$1');
$routes->delete('/users/(:num)', 'UsersController::destroy/$1');
$routes->post('/users/(:num)/restore', 'UsersController::restore');

// Organizations
$routes->get('/organizations', 'OrganizationsController::index');
$routes->get('/organizations/create', 'OrganizationsController::create');
$routes->post('/organizations', 'OrganizationsController::store');
$routes->get('/organizations/(:num)/edit', 'OrganizationsController::edit/$1');
$routes->put('/organizations/(:num)', 'OrganizationsController::update/$1');
$routes->delete('/organizations/(:num)', 'OrganizationsController::destroy/$1');
$routes->post('/organizations/(:num)/restore', 'OrganizationsController::restore');

// Contacts
$routes->get('/contacts', 'ContactsController::index');
$routes->get('/contacts/create', 'ContactsController::create');
$routes->post('/contacts', 'ContactsController::store');
$routes->get('/contacts/(:num)/edit', 'ContactsController::edit/$1');
$routes->put('/contacts/(:num)', 'ContactsController::update/$1');
$routes->delete('/contacts/(:num)', 'ContactsController::destroy/$1');
$routes->post('/contacts/(:num)/restore', 'ContactsController::restore');

// Reports
$routes->get('/reports', 'ReportsController::index');

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
if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
