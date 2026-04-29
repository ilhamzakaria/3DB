<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
// home
$routes->get('/home', 'Home::index');
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

// bahan baku (gudang)
$routes->get('/gudang/bahan_baku', 'BahanBaku::index');
$routes->post('/gudang/bahan_baku/tambahData', 'BahanBaku::tambahData');
$routes->post('/gudang/bahan_baku/delete/(:num)', 'BahanBaku::delete/$1');
$routes->delete('/gudang/bahan_baku/delete/(:num)', 'BahanBaku::delete/$1');
$routes->get('/gudang/bahan_baku/edit/(:num)', 'BahanBaku::edit/$1');
$routes->post('/gudang/bahan_baku/update', 'BahanBaku::update');
$routes->post('/gudang/bahan_baku/update/(:num)', 'BahanBaku::update/$1');

// packaging (gudang)
$routes->get('/gudang/packaging', 'Packaging::index');
$routes->post('/gudang/packaging/tambahData', 'Packaging::tambahData');
$routes->post('/gudang/packaging/delete/(:num)', 'Packaging::delete/$1');
$routes->delete('/gudang/packaging/delete/(:num)', 'Packaging::delete/$1');
$routes->get('/gudang/packaging/edit/(:num)', 'Packaging::edit/$1');
$routes->post('/gudang/packaging/update', 'Packaging::update');
$routes->post('/gudang/packaging/update/(:num)', 'Packaging::update/$1');

// masterbatch (gudang)
$routes->get('/gudang/masterbatch', 'Masterbatch::index');
$routes->post('/gudang/masterbatch/tambahData', 'Masterbatch::tambahData');
$routes->post('/gudang/masterbatch/delete/(:num)', 'Masterbatch::delete/$1');
$routes->delete('/gudang/masterbatch/delete/(:num)', 'Masterbatch::delete/$1');
$routes->get('/gudang/masterbatch/edit/(:num)', 'Masterbatch::edit/$1');
$routes->post('/gudang/masterbatch/update', 'Masterbatch::update');
$routes->post('/gudang/masterbatch/update/(:num)', 'Masterbatch::update/$1');

// galon_reject (gudang)
$routes->get('/gudang/galon_reject', 'GalonReject::index');
$routes->post('/gudang/galon_reject/tambahData', 'GalonReject::tambahData');
$routes->post('/gudang/galon_reject/delete/(:num)', 'GalonReject::delete/$1');
$routes->delete('/gudang/galon_reject/delete/(:num)', 'GalonReject::delete/$1');
$routes->get('/gudang/galon_reject/edit/(:num)', 'GalonReject::edit/$1');
$routes->post('/gudang/galon_reject/update', 'GalonReject::update');
$routes->post('/gudang/galon_reject/update/(:num)', 'GalonReject::update/$1');

// gilingan_galon (gudang)
$routes->get('/gudang/gilingan_galon', 'GilinganGalon::index');
$routes->post('/gudang/gilingan_galon/tambahData', 'GilinganGalon::tambahData');
$routes->post('/gudang/gilingan_galon/delete/(:num)', 'GilinganGalon::delete/$1');
$routes->delete('/gudang/gilingan_galon/delete/(:num)', 'GilinganGalon::delete/$1');
$routes->post('/gudang/gilingan_galon/update/(:num)', 'GilinganGalon::update/$1');

// stiker (gudang)
$routes->get('/gudang/stiker', 'Stiker::index');
$routes->post('/gudang/stiker/tambahData', 'Stiker::tambahData');
$routes->post('/gudang/stiker/delete/(:num)', 'Stiker::delete/$1');
$routes->delete('/gudang/stiker/delete/(:num)', 'Stiker::delete/$1');
$routes->post('/gudang/stiker/update/(:num)', 'Stiker::update/$1');

// reject_produksi (gudang)
$routes->get('/gudang/reject_produksi', 'RejectProduksi::index');
$routes->post('/gudang/reject_produksi/tambahData', 'RejectProduksi::tambahData');
$routes->post('/gudang/reject_produksi/delete/(:num)', 'RejectProduksi::delete/$1');
$routes->delete('/gudang/reject_produksi/delete/(:num)', 'RejectProduksi::delete/$1');
$routes->post('/gudang/reject_produksi/update/(:num)', 'RejectProduksi::update/$1');

// mesin modules (gudang)
$mesinModules = ['becum', 'powerjet1', 'powerjet2', 'ccm1', 'ccm2', 'ips1', 'ips2', 'ips3', 'ips4'];
foreach ($mesinModules as $m) {
    $ctrl = 'Mesin' . ucfirst($m);
    $routes->get("/gudang/mesin_$m", "$ctrl::index");
    $routes->post("/gudang/mesin_$m/tambahData", "$ctrl::tambahData");
    $routes->post("/gudang/mesin_$m/delete/(:num)", "$ctrl::delete/$1");
    $routes->delete("/gudang/mesin_$m/delete/(:num)", "$ctrl::delete/$1");
    $routes->post("/gudang/mesin_$m/update/(:num)", "$ctrl::update/$1");
}


// bahan baku legacy aliases
$routes->get('/Vbahan_baku', 'BahanBaku::index');
$routes->get('/bahan_baku', 'BahanBaku::index');
$routes->post('/bahan_baku/tambahData', 'BahanBaku::tambahData');
$routes->post('/bahan_baku/delete/(:num)', 'BahanBaku::delete/$1');
$routes->delete('/bahan_baku/delete/(:num)', 'BahanBaku::delete/$1');
$routes->get('/bahan_baku/edit/(:num)', 'BahanBaku::edit/$1');
$routes->post('/bahan_baku/update', 'BahanBaku::update');
$routes->post('/bahan_baku/update/(:num)', 'BahanBaku::update/$1');


// admin
$routes->get('/admin', 'Admin::index', ['filter' => 'admin']);
$routes->post('/admin/tambahData', 'Admin::tambahData');
$routes->delete('/admin/delete/(:num)', 'Admin::delete/$1');
$routes->get('/admin/edit/(:num)', 'Admin::edit/$1');
$routes->post('/admin/update/(:num)', 'Admin::update/$1');

// chat (ppic, produksi, gudang)
$routes->get('/pesan', 'Pesan::index');
$routes->get('/pesan/getMessages', 'Pesan::getMessages');
$routes->get('/pesan/getUnreadCount', 'Pesan::getUnreadCount');
$routes->post('/pesan/sendMessage', 'Pesan::sendMessage');
$routes->get('/pesan/download/(:num)', 'Pesan::download/$1');

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
$routes->get('/ppic/restore/(:num)', 'Ppic::restore/$1');
$routes->get('/ppic/delete_permanent/(:num)', 'Ppic::deletePermanent/$1');
$routes->get('/ppic/get_trash', 'Ppic::get_trash');
$routes->get('/ppic/exportExcel', 'Ppic::exportExcel');
// Export to Excel
$routes->get('dashboard/export-excel', 'Dashboard::exportExcel');

// Export berbagai data
$routes->get('export/dashboard', 'Export::dashboard');
$routes->get('export/home', 'Export::home');
$routes->get('export/kontak', 'Export::kontak');
$routes->get('export/bahan_baku', 'Export::bahanBaku');
$routes->get('export/packaging', 'Export::packaging');
$routes->get('export/masterbatch', 'Export::masterbatch');
$routes->get('export/galon_reject', 'Export::galonReject');
$routes->get('export/gilingan_galon', 'Export::gilinganGalon');
$routes->get('export/stiker', 'Export::stiker');
$routes->get('export/reject_produksi', 'Export::rejectProduksi');
$routes->get('export/mesin_becum', 'Export::mesinBecum');
$routes->get('export/mesin_powerjet1', 'Export::mesinPowerjet1');
$routes->get('export/mesin_powerjet2', 'Export::mesinPowerjet2');
$routes->get('export/mesin_ccm1', 'Export::mesinCcm1');
$routes->get('export/mesin_ccm2', 'Export::mesinCcm2');
$routes->get('export/mesin_ips1', 'Export::mesinIps1');
$routes->get('export/mesin_ips2', 'Export::mesinIps2');
$routes->get('export/mesin_ips3', 'Export::mesinIps3');
$routes->get('export/mesin_ips4', 'Export::mesinIps4');
$routes->get('export/produksi', 'Export::produksi');



// Produksi (New Module)
$routes->get('/produksi', 'Produksi::index');
$routes->get('/produksi/tambah', 'Produksi::tambah');
$routes->post('/produksi/simpan', 'Produksi::simpan');
$routes->get('/produksi/edit/(:num)', 'Produksi::edit/$1');
$routes->post('/produksi/update/(:num)', 'Produksi::update/$1');
$routes->get('/produksi/delete/(:num)', 'Produksi::delete/$1');
$routes->get('/produksi/trash', 'Produksi::trash');
$routes->get('/produksi/restore/(:num)', 'Produksi::restore/$1');
$routes->get('/produksi/delete_permanent/(:num)', 'Produksi::deletePermanent/$1');
$routes->get('/produksi/get_last_data/(:any)', 'Produksi::get_last_data/$1');
$routes->get('/produksi/detail/(:num)', 'Produksi::detail/$1');
$routes->get('/produksi/get_trash', 'Produksi::get_trash');
$routes->get('/produksi/get_edit_form/(:num)', 'Produksi::get_edit_form/$1');
$routes->get('/produksi/get_detail_modal/(:num)', 'Produksi::get_detail_modal/$1');

