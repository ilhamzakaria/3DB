<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// home
$routes->get('/home', 'Home::index');
$routes->get('/mesin1', 'Home::mesin1');
$routes->get('/mesin2', 'Home::mesin2');
$routes->get('/dashboard', 'Dashboard::index', ['filter' => 'manager']);
$routes->get('/laporan', 'Laporan::index', ['filter' => 'manager']);

// login
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::processLogin');
$routes->get('/logout', 'Auth::logout');

// filter
$routes->get('/laporan', 'Laporan::index', ['filter' => 'auth']);

// tambah laporan
$routes->post('/laporan/tambahData', 'Laporan::tambahData');

// delete laporan
$routes->delete('/laporan/delete/(:num)', 'Laporan::delete/$1');

// $routes->post('/laporan/ubahData/(:num)', 'Laporan::ubahData/$1');
$routes->get('/laporan/edit/(:num)', 'Laporan::edit/$1');
$routes->post('/laporan/update/(:num)', 'Laporan::update/$1');

// operator
$routes->get('/operator', 'Operator::index');
$routes->post('/operator/tambahData', 'Operator::tambahData');
$routes->delete('/operator/delete/(:num)', 'Operator::delete/$1');
$routes->get('/operator/edit/(:num)', 'Operator::edit/$1');
$routes->post('/operator/update/(:num)', 'Operator::update/$1');

// gudang
$routes->get('/gudang', 'Gudang::index');
$routes->post('/gudang/tambahData', 'Gudang::tambahData');
$routes->delete('/gudang/delete/(:num)', 'Gudang::delete/$1');
$routes->get('/gudang/edit/(:num)', 'Gudang::edit/$1');
$routes->post('/gudang/update/(:num)', 'Gudang::update/$1');

// produksi
$routes->get('/produksi', 'Produksi::index');
$routes->post('/produksi/tambahData', 'Produksi::tambahData');
$routes->delete('/produksi/delete/(:num)', 'Produksi::delete/$1');
$routes->get('/produksi/edit/(:num)', 'Produksi::edit/$1');
$routes->post('/produksi/update/(:num)', 'Produksi::update/$1');

// admin
$routes->get('/admin', 'Admin::index', ['filter' => 'admin']);
$routes->post('/admin/tambahData', 'Admin::tambahData');
$routes->delete('/admin/delete/(:num)', 'Admin::delete/$1');
$routes->get('/admin/edit/(:num)', 'Admin::edit/$1');
$routes->post('/admin/update/(:num)', 'Admin::update/$1');

// chat (ppic, produksi, gudang)
$routes->get('/chat', 'Chat::index');
$routes->get('/chat/getMessages', 'Chat::getMessages');
$routes->get('/chat/getUnreadCount', 'Chat::getUnreadCount');
$routes->post('/chat/sendMessage', 'Chat::sendMessage');
$routes->get('/chat/download/(:num)', 'Chat::download/$1');

// notifikasi (badge + list)
$routes->get('/notifikasi', 'Notifikasi::index');
$routes->get('/notifikasi/count', 'Notifikasi::count');
$routes->get('/notifikasi/read/(:num)', 'Notifikasi::read/$1');

// ppic admin
$routes->get('/ppic', 'Ppic::index', ['filter' => 'ppic']);
$routes->post('/ppic/tambahData', 'Ppic::tambahData');
$routes->delete('/ppic/delete/(:num)', 'Ppic::delete/$1');
$routes->get('/ppic/edit/(:num)', 'Ppic::edit/$1');
$routes->post('/ppic/update', 'Ppic::update');
$routes->post('/ppic/update/(:num)', 'Ppic::update/$1');
// $routes->get('/dashboard', 'Dashboard::index', ['filter' => 'manager']);

// Export to Excel
$routes->get('dashboard/export-excel', 'Dashboard::exportExcel');
// $routes->get('test-excel', 'TestExcel::index');

// Export berbagai data
$routes->get('export/dashboard', 'Export::dashboard');
$routes->get('export/home', 'Export::home');
$routes->get('export/kontak', 'Export::kontak');
