<?php
require 'vendor/autoload.php';

// Load CodeIgniter environment
$_ENV['CI_ENVIRONMENT'] = 'development';

\Config\Services::autoloader()->addNamespace('Config', APPPATH . 'Config');
\Config\Services::autoloader()->addNamespace('App', APPPATH);

// Get the database instance
$db = \Config\Database::connect('warehouse');
echo 'Connected to: ' . $db->getDatabase() . PHP_EOL;

// Show table columns
echo "Checking if table exists..." . PHP_EOL;
$result = $db->query('SHOW TABLES LIKE "barang_masuk_dari_produksi"')->getResultArray();
if (count($result) == 0) {
    echo "ERROR: Table barang_masuk_dari_produksi does not exist!" . PHP_EOL;
    echo "Available tables:" . PHP_EOL;
    $tables = $db->query('SHOW TABLES')->getResultArray();
    foreach ($tables as $t) {
        echo '  - ' . array_values($t)[0] . PHP_EOL;
    }
    exit(1);
}

echo "Table exists! Columns:" . PHP_EOL;
$result = $db->query('DESCRIBE barang_masuk_dari_produksi')->getResultArray();
foreach ($result as $col) {
    echo '  - ' . $col['Field'] . ' (' . $col['Type'] . ')' . PHP_EOL;
}

// Test insert
echo "\nTesting insert..." . PHP_EOL;
$testData = [
    'no_dokumen' => 'TEST-' . date('YmdHis'),
    'tanggal' => date('Y-m-d'),
    'no_spk' => 'SPK-001',
    'nama_produksi' => 'Test Produksi',
    'kode_barang' => 'KB-001',
    'nama_barang' => 'Test Barang',
    'satuan' => 'pcs',
    'jumlah_produksi' => 100,
    'jumlah_reject' => 0,
    'jumlah_diterima' => 100,
    'nama_penerima' => 'Admin',
    'catatan' => 'Test',
    'status' => 'diterima'
];

try {
    $model = new \App\Models\MBarangMasukDariProduksi();
    $id = $model->insert($testData);
    echo "SUCCESS! Inserted ID: " . $id . PHP_EOL;

    // Cleanup
    $model->delete($id);
    echo "Test data cleaned up." . PHP_EOL;
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . PHP_EOL;
}
