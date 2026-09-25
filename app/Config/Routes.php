<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// === TEST ROUTES (Remove after debugging) ===
$routes->get('test-db', 'TestDb::index');

// home
$routes->get('/home', 'Home::index');
$routes->get('/laporan', 'Laporan::index', ['filter' => 'manager']);


// login
$routes->get('/login', 'Auth::login');
$routes->post('/login', 'Auth::processLogin');
$routes->get('/logout', 'Auth::logout');
$routes->get('/login-debug', 'Auth::loginDebug');

// profile - available for all logged in users
$routes->get('/profile', 'Profile::index');
$routes->post('/profile/upload-foto', 'Profile::uploadFoto');
$routes->post('/profile/upload-ttd', 'Profile::uploadTtd');
$routes->post('/profile/auto-upload-ttd', 'Profile::autoUploadTtd');
$routes->get('/profile/preview-foto/(:num)', 'Profile::previewFoto/$1');
$routes->get('/profile/preview-ttd/(:num)', 'Profile::previewTtd/$1');
$routes->get('/profile/get-ttd-for-form', 'Profile::getTtdForForm');

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

// pengiriman (gudang)
$routes->get('/gudang/pengiriman', 'Pengiriman::index');
$routes->post('/gudang/pengiriman/tambahData', 'Pengiriman::tambahData');
$routes->post('/gudang/pengiriman/delete/(:num)', 'Pengiriman::delete/$1');
$routes->delete('/gudang/pengiriman/delete/(:num)', 'Pengiriman::delete/$1');
$routes->get('/gudang/pengiriman/edit/(:num)', 'Pengiriman::edit/$1');
$routes->post('/gudang/pengiriman/update', 'Pengiriman::update');
$routes->post('/gudang/pengiriman/update/(:num)', 'Pengiriman::update/$1');

// BAM (gudang)
$routes->get('/gudang/bam', 'Bam::index');
$routes->post('/gudang/bam/tambahData', 'Bam::tambahData');
$routes->post('/gudang/bam/delete/(:num)', 'Bam::delete/$1');
$routes->delete('/gudang/bam/delete/(:num)', 'Bam::delete/$1');
$routes->post('/gudang/bam/update', 'Bam::update');
$routes->post('/gudang/bam/update/(:num)', 'Bam::update/$1');

// finish good (gudang)
$routes->get('/gudang/finish_good', 'FinishGood::index');
$routes->post('/gudang/finish_good/tambahData', 'FinishGood::tambahData');
$routes->post('/gudang/finish_good/delete/(:num)', 'FinishGood::delete/$1');
$routes->delete('/gudang/finish_good/delete/(:num)', 'FinishGood::delete/$1');
$routes->get('/gudang/finish_good/edit/(:num)', 'FinishGood::edit/$1');
$routes->post('/gudang/finish_good/update', 'FinishGood::update');
$routes->post('/gudang/finish_good/update/(:num)', 'FinishGood::update/$1');

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
    $routes->post("/gudang/mesin_$m/completeSpk/(:num)", "$ctrl::completeSpk/$1");

    // Brand management
    $routes->get("/gudang/mesin_$m/brands-json", "$ctrl::getBrandsJson");
    $routes->post("/gudang/mesin_$m/store-brand", "$ctrl::storeBrand");
    $routes->delete("/gudang/mesin_$m/delete-brand/(:num)", "$ctrl::deleteBrand/$1");
    $routes->post("/gudang/mesin_$m/delete-brand/(:num)", "$ctrl::deleteBrand/$1");

    // Komposisi management (hanya becum)
    if ($m === 'becum') {
        $routes->get("/gudang/mesin_$m/komposisi-json", "$ctrl::getKomposisiJson");
        $routes->post("/gudang/mesin_$m/store-komposisi", "$ctrl::storeKomposisi");
        $routes->post("/gudang/mesin_$m/delete-komposisi/(:num)", "$ctrl::deleteKomposisi/$1");
    }
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

// pengiriman legacy aliases
$routes->get('/pengiriman', 'Pengiriman::index');
$routes->post('/pengiriman/tambahData', 'Pengiriman::tambahData');
$routes->post('/pengiriman/delete/(:num)', 'Pengiriman::delete/$1');
$routes->delete('/pengiriman/delete/(:num)', 'Pengiriman::delete/$1');
$routes->get('/pengiriman/edit/(:num)', 'Pengiriman::edit/$1');
$routes->post('/pengiriman/update', 'Pengiriman::update');
$routes->post('/pengiriman/update/(:num)', 'Pengiriman::update/$1');

// finish good legacy aliases
$routes->get('/finish_good', 'FinishGood::index');
$routes->post('/finish_good/tambahData', 'FinishGood::tambahData');
$routes->post('/finish_good/delete/(:num)', 'FinishGood::delete/$1');
$routes->delete('/finish_good/delete/(:num)', 'FinishGood::delete/$1');
$routes->get('/finish_good/edit/(:num)', 'FinishGood::edit/$1');
$routes->post('/finish_good/update', 'FinishGood::update');
$routes->post('/finish_good/update/(:num)', 'FinishGood::update/$1');


// admin
$routes->get('/admin', 'Admin::index', ['filter' => 'admin']);
$routes->post('/admin/tambahData', 'Admin::tambahData');
$routes->delete('/admin/delete/(:num)', 'Admin::delete/$1');
$routes->get('/admin/edit/(:num)', 'Admin::edit/$1');
$routes->post('/admin/update/(:num)', 'Admin::update/$1');

// warehouse2 - Main page (ppic & produksi bisa view, warehouse2 full access)
$routes->get('/warehouse2', 'Warehouse2::index', ['filter' => 'readOnlyWarehouse2']);

// warehouse2 - Debug endpoint (TEMPORARY - REMOVE after fixing)
$routes->get('/warehouse2/debug-insert-test', 'Warehouse2::debugInsertTest', ['filter' => 'auth']);

// warehouse2 - API endpoints untuk fetch data (read-only, semua role bisa akses)
$routes->get('/warehouse2/api/barang-masuk-pemasok', 'Warehouse2::getBarangMasukPemasok');
$routes->get('/warehouse2/api/barang-masuk-produksi', 'Warehouse2::getBarangMasukProduksi');
$routes->get('/warehouse2/api/barang-keluar-pembeli', 'Warehouse2::getBarangKeluarPembeli');
$routes->get('/warehouse2/api/barang-masuk-dari-produksi', 'Warehouse2::getBarangMasukDariProduksi');
$routes->get('/warehouse2/api/orowarehouse', 'Warehouse2::getOrowarehouse');
$routes->get('/warehouse2/api/orowarehouse/by-spk', 'Warehouse2::getOrowarehouseBySpk');
$routes->get('/warehouse2/api/orowarehouse/spk-list', 'Warehouse2::getSpkList');
$routes->get('/warehouse2/api/orowarehouse/filters', 'Warehouse2::getOrowarehouseFilters');
$routes->get('/warehouse2/api/orowarehouse-trash', 'Warehouse2::getOrowarehouseTrash');
$routes->get('/warehouse2/export-orowarehouse', 'Warehouse2::exportOrowarehouse');

// warehouse2 - CRUD barang masuk pemasok (hanya manager warehouse2)
$routes->post('/warehouse2/store-pemasok', 'Warehouse2::storePemasok', ['filter' => 'auth']);
$routes->post('/warehouse2/update-pemasok/(:num)', 'Warehouse2::updatePemasok/$1', ['filter' => 'auth']);
$routes->post('/warehouse2/delete-pemasok/(:num)', 'Warehouse2::deletePemasok/$1', ['filter' => 'auth']);
$routes->delete('/warehouse2/delete-pemasok/(:num)', 'Warehouse2::deletePemasok/$1', ['filter' => 'auth']);
$routes->post('/warehouse2/update-status-pemasok/(:num)', 'Warehouse2::updateStatusPemasok/$1', ['filter' => 'auth']);

// warehouse2 - CRUD barang masuk produksi (hanya manager warehouse2)
$routes->post('/warehouse2/store-produksi', 'Warehouse2::storeProduksi', ['filter' => 'auth']);
$routes->post('/warehouse2/update-produksi/(:num)', 'Warehouse2::updateProduksi/$1', ['filter' => 'auth']);
$routes->post('/warehouse2/update-status-produksi/(:num)', 'Warehouse2::updateStatusProduksi/$1', ['filter' => 'auth']);
$routes->delete('/warehouse2/delete-produksi/(:num)', 'Warehouse2::deleteProduksi/$1', ['filter' => 'auth']);

// warehouse2 - CRUD barang keluar pembeli (hanya manager warehouse2)
$routes->post('/warehouse2/store-pembeli', 'Warehouse2::storePembeli', ['filter' => 'auth']);
$routes->post('/warehouse2/update-pembeli/(:num)', 'Warehouse2::updatePembeli/$1', ['filter' => 'auth']);
$routes->post('/warehouse2/update-status-pembeli/(:num)', 'Warehouse2::updateStatusPembeli/$1', ['filter' => 'auth']);
$routes->delete('/warehouse2/delete-pembeli/(:num)', 'Warehouse2::deletePembeli/$1', ['filter' => 'auth']);

// warehouse2 - CRUD barang masuk dari produksi (hanya manager warehouse2)
$routes->post('/warehouse2/store-masuk-produksi', 'Warehouse2::storeMasukProduksi', ['filter' => 'auth']);
$routes->delete('/warehouse2/delete-masuk-produksi/(:num)', 'Warehouse2::deleteMasukProduksi/$1', ['filter' => 'auth']);

// warehouse2 - OROWAREHOUSE write endpoints (hanya manager warehouse2)
$routes->post('/warehouse2/store-orowarehouse', 'Warehouse2::storeOrowarehouse', ['filter' => 'auth']);
$routes->post('/warehouse2/update-orowarehouse/(:num)', 'Warehouse2::updateOrowarehouse/$1', ['filter' => 'auth']);
$routes->delete('/warehouse2/delete-orowarehouse/(:num)', 'Warehouse2::deleteOrowarehouse/$1', ['filter' => 'auth']);
$routes->post('/warehouse2/restore-orowarehouse/(:num)', 'Warehouse2::restoreOrowarehouse/$1', ['filter' => 'auth']);
$routes->delete('/warehouse2/delete-permanent-orowarehouse/(:num)', 'Warehouse2::deletePermanentOrowarehouse/$1', ['filter' => 'auth']);

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
$routes->get('/ppic', 'Ppic::index', ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/tambahData', 'Ppic::tambahData', ['filter' => 'operatorReadOnly']);
$routes->delete('/ppic/delete/(:num)', 'Ppic::delete/$1', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/edit/(:num)', 'Ppic::edit/$1');
$routes->post('/ppic/update', 'Ppic::update', ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/update/(:num)', 'Ppic::update/$1', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/restore/(:num)', 'Ppic::restore/$1', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/delete_permanent/(:num)', 'Ppic::deletePermanent/$1', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/get_trash', 'Ppic::get_trash');
$routes->get('/ppic/exportExcel', 'Ppic::exportExcel');
$routes->post('/ppic/createUser', 'Ppic::createUser', ['filter' => 'operatorReadOnly']);

// ppic — Finish Good (Galon / Cap Galon / Freeform / Screw Cap)
$routes->get('/ppic/finish_good',                  'FinishGoodPpic::galon',        ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/galon',                  'FinishGoodPpic::galon',        ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/galon/tambah',          'FinishGoodPpic::tambahGalon',  ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/galon/update/(:num)',   'FinishGoodPpic::updateGalon/$1', ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/galon/delete/(:num)',   'FinishGoodPpic::deleteGalon/$1', ['filter' => 'operatorReadOnly']);
$routes->delete('/ppic/finish_good/galon/delete/(:num)',   'FinishGoodPpic::deleteGalon/$1', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/galon/get-trash',        'FinishGoodPpic::getTrash', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/galon/restore/(:num)',   'FinishGoodPpic::restore', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/galon/delete-permanent/(:num)', 'FinishGoodPpic::deletePermanent', ['filter' => 'operatorReadOnly']);

$routes->get('/ppic/finish_good/cap_galon',                   'FinishGoodPpic::capGalon',        ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/cap_galon/tambah',           'FinishGoodPpic::tambahCapGalon',  ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/cap_galon/update/(:num)',    'FinishGoodPpic::updateCapGalon/$1', ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/cap_galon/delete/(:num)',    'FinishGoodPpic::deleteCapGalon/$1', ['filter' => 'operatorReadOnly']);
$routes->delete('/ppic/finish_good/cap_galon/delete/(:num)',    'FinishGoodPpic::deleteCapGalon/$1', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/cap_galon/get-trash',         'FinishGoodPpic::getTrash', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/cap_galon/restore/(:num)',    'FinishGoodPpic::restore', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/cap_galon/delete-permanent/(:num)', 'FinishGoodPpic::deletePermanent', ['filter' => 'operatorReadOnly']);

$routes->get('/ppic/finish_good/freeform',                  'FinishGoodPpic::freeform',        ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/freeform/tambah',          'FinishGoodPpic::tambahFreeform',  ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/freeform/update/(:num)',   'FinishGoodPpic::updateFreeform/$1', ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/freeform/delete/(:num)',   'FinishGoodPpic::deleteFreeform/$1', ['filter' => 'operatorReadOnly']);
$routes->delete('/ppic/finish_good/freeform/delete/(:num)',   'FinishGoodPpic::deleteFreeform/$1', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/freeform/get-trash',        'FinishGoodPpic::getTrash', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/freeform/restore/(:num)',   'FinishGoodPpic::restore', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/freeform/delete-permanent/(:num)', 'FinishGoodPpic::deletePermanent', ['filter' => 'operatorReadOnly']);

$routes->get('/ppic/finish_good/screw_cap',                  'FinishGoodPpic::screwCap',          ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/screw_cap/tambah',          'FinishGoodPpic::tambahScrewCap',    ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/screw_cap/update/(:num)',   'FinishGoodPpic::updateScrewCap/$1', ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/screw_cap/delete/(:num)',   'FinishGoodPpic::deleteScrewCap/$1', ['filter' => 'operatorReadOnly']);
$routes->delete('/ppic/finish_good/screw_cap/delete/(:num)',   'FinishGoodPpic::deleteScrewCap/$1', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/screw_cap/get-trash',        'FinishGoodPpic::getTrash', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/screw_cap/restore/(:num)',   'FinishGoodPpic::restore', ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/screw_cap/delete-permanent/(:num)', 'FinishGoodPpic::deletePermanent', ['filter' => 'operatorReadOnly']);

// ppic — Finish Good Import Excel Routes
$routes->post('/ppic/finish_good/(:segment)/import-preview',  'FinishGoodPpic::importPreview/$1', ['filter' => 'operatorReadOnly']);
$routes->post('/ppic/finish_good/(:segment)/import-commit',   'FinishGoodPpic::importCommit/$1',  ['filter' => 'operatorReadOnly']);
$routes->get('/ppic/finish_good/(:segment)/template',         'FinishGoodPpic::downloadTemplate/$1', ['filter' => 'operatorReadOnly']);

// ppic — Finish Good Add Produk Baru (AJAX endpoint)
$routes->post('/ppic/finish_good/(:segment)/add-produk',      'FinishGoodPpic::addProduk/$1', ['filter' => 'operatorReadOnly']);

// mesin modules (ppic / LOG MESIN)
$ppicMesinModules = ['becum', 'powerjet1', 'powerjet2', 'ccm1', 'ccm2', 'ips1', 'ips2', 'ips3', 'ips4'];
foreach ($ppicMesinModules as $m) {
    $ctrl = 'Mesin' . ucfirst($m);
    $routes->get("/ppic/mesin_$m", "$ctrl::index", ['filter' => 'operatorReadOnly']);
    $routes->post("/ppic/mesin_$m/tambahData", "$ctrl::tambahData", ['filter' => 'operatorReadOnly']);
    $routes->post("/ppic/mesin_$m/delete/(:num)", "$ctrl::delete/$1", ['filter' => 'operatorReadOnly']);
    $routes->delete("/ppic/mesin_$m/delete/(:num)", "$ctrl::delete/$1", ['filter' => 'operatorReadOnly']);
    $routes->post("/ppic/mesin_$m/update/(:num)", "$ctrl::update/$1", ['filter' => 'operatorReadOnly']);
    $routes->post("/ppic/mesin_$m/completeSpk/(:num)", "$ctrl::completeSpk/$1", ['filter' => 'operatorReadOnly']);
    $routes->get("/ppic/mesin_$m/get-trash", "$ctrl::getTrash", ['filter' => 'operatorReadOnly']);
    $routes->get("/ppic/mesin_$m/restore/(:num)", "$ctrl::restore/$1", ['filter' => 'operatorReadOnly']);
    $routes->get("/ppic/mesin_$m/delete-permanent/(:num)", "$ctrl::deletePermanent/$1", ['filter' => 'operatorReadOnly']);

    // Brand management
    $routes->get("/ppic/mesin_$m/brands-json", "$ctrl::getBrandsJson", ['filter' => 'operatorReadOnly']);
    $routes->post("/ppic/mesin_$m/store-brand", "$ctrl::storeBrand", ['filter' => 'operatorReadOnly']);
    $routes->delete("/ppic/mesin_$m/delete-brand/(:num)", "$ctrl::deleteBrand/$1", ['filter' => 'operatorReadOnly']);
    $routes->post("/ppic/mesin_$m/delete-brand/(:num)", "$ctrl::deleteBrand/$1", ['filter' => 'operatorReadOnly']);

    // Komposisi management (semua mesin)
    $routes->get("/ppic/mesin_$m/komposisi-json", "$ctrl::getKomposisiJson", ['filter' => 'operatorReadOnly']);
    $routes->post("/ppic/mesin_$m/store-komposisi", "$ctrl::storeKomposisi", ['filter' => 'operatorReadOnly']);
    $routes->post("/ppic/mesin_$m/delete-komposisi/(:num)", "$ctrl::deleteKomposisi/$1", ['filter' => 'operatorReadOnly']);
}

// gudang mesin modules (LOG MESIN non-ppic context)
foreach ($ppicMesinModules as $m) {
    $ctrl = 'Mesin' . ucfirst($m);
    $routes->get("/gudang/mesin_$m/get-trash", "$ctrl::getTrash", ['filter' => 'auth']);
    $routes->get("/gudang/mesin_$m/restore/(:num)", "$ctrl::restore/$1", ['filter' => 'auth']);
    $routes->get("/gudang/mesin_$m/delete-permanent/(:num)", "$ctrl::deletePermanent/$1", ['filter' => 'auth']);
}

// ppic export routes — LOG MESIN (menggunakan DB oroppic)
$routes->get('/ppic/export/mesin_(:segment)', 'PpicExport::mesin/$1', ['filter' => 'ppic']);

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
// Produksi mesin sub-pages (each mesin has its own route, like PPIC)
$produksiMesinModules = ['becum', 'powerjet1', 'powerjet2', 'ccm1', 'ccm2', 'ips1', 'ips2', 'ips3', 'ips4'];
foreach ($produksiMesinModules as $m) {
    $routes->get("/produksi/mesin_$m", "Produksi::mesinIndex/$m", ['filter' => 'readOnlyProduksi']);
}

// GET routes: ppic & warehouse2 bisa view (read-only)
$routes->get('/produksi', 'Produksi::index', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/tambah', 'Produksi::tambah', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/edit/(:num)', 'Produksi::edit/$1', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/delete/(:num)', 'Produksi::delete/$1', ['filter' => 'managerProduksi']);
$routes->get('/produksi/trash', 'Produksi::trash', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/restore/(:num)', 'Produksi::restore/$1', ['filter' => 'managerProduksi']);
$routes->get('/produksi/delete_permanent/(:num)', 'Produksi::deletePermanent/$1', ['filter' => 'managerProduksi']);
$routes->get('/produksi/get_last_data/(:any)', 'Produksi::get_last_data/$1', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/get_ppic_spk/(:any)', 'Produksi::getPpicSpkDetail/$1', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/get_ppic_spk', 'Produksi::getPpicSpkDetail', ['filter' => 'readOnlyProduksi']);
$routes->get('/api/spk-detail/(:any)', 'Produksi::getPpicSpkDetail/$1', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/get_produksi_spk_list', 'Produksi::getProduksiSpkList', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/detail/(:num)', 'Produksi::detail/$1', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/get_trash', 'Produksi::get_trash', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/get_edit_form/(:num)', 'Produksi::get_edit_form/$1', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/get_detail_modal/(:num)', 'Produksi::get_detail_modal/$1', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/get-user-ttd/(:any)', 'Produksi::getUserTtd/$1', ['filter' => 'auth']);
$routes->get('/produksi/get-user-ttd', 'Produksi::getUserTtd', ['filter' => 'auth']);
$routes->get('/produksi/get-shifts', 'Produksi::getShifts', ['filter' => 'readOnlyProduksi']);
$routes->get('/produksi/get-target-by-shift', 'Produksi::getTargetByShift', ['filter' => 'readOnlyProduksi']);
// POST routes: hanya manager, admin, operator
$routes->post('/produksi/simpan', 'Produksi::simpan', ['filter' => 'managerProduksi']);
$routes->post('/produksi/update/(:num)', 'Produksi::update/$1', ['filter' => 'managerProduksi']);

// reject types (produksi module)
$routes->get('/reject-type/getAll', 'RejectType::getAll', ['filter' => 'auth']);
$routes->get('/reject-type/getJson', 'RejectType::getJson', ['filter' => 'auth']);
$routes->get('/reject-type/manage', 'RejectType::manage', ['filter' => 'auth']);
$routes->post('/reject-type/create', 'RejectType::create', ['filter' => 'auth']);
$routes->post('/reject-type/store', 'RejectType::store', ['filter' => 'auth']);
$routes->post('/reject-type/update/(:num)', 'RejectType::update/$1', ['filter' => 'auth']);
$routes->post('/reject-type/toggle/(:num)', 'RejectType::toggle', ['filter' => 'auth']);
$routes->post('/reject-type/delete/(:num)', 'RejectType::delete/$1', ['filter' => 'auth']);
$routes->post('/reject-type/destroy/(:num)', 'RejectType::destroy/$1', ['filter' => 'auth']);

// Migration routes
$routes->get('/MigrateDb/addJumlah', 'MigrateDb::addJumlah');

// API Routes — Mesin Data (accessible to all logged-in roles: ppic, produksi, gudang, etc.)
$routes->get('/api/mesin-data/latest', 'Api\MesinDataApi::latest', ['filter' => 'auth']);
$routes->get('/api/mesin-data/(:segment)', 'Api\MesinDataApi::show/$1', ['filter' => 'auth']);

// Debug routes
$routes->get('/debug/check', 'DbCheck::check');
$routes->get('/debug/compare-data', 'DbCheck::compareData');
