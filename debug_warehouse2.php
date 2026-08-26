<?php
// Debug script for Warehouse2 tambah data error

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'public/index.php';

// Test database connection
$db = \Config\Database::connect('warehouse');

echo "=== Database Test ===\n";
echo "Database Name: " . $db->getDatabase() . "\n";
echo "Database Connected: " . ($db->connID ? 'YES' : 'NO') . "\n\n";

// Check if table exists
echo "=== Checking Table Structure ===\n";
$tables = $db->query("SHOW TABLES")->getResultArray();
$tableNames = array_map(function ($t) {
    return array_values($t)[0];
}, $tables);
echo "Tables found: " . count($tableNames) . "\n";
echo "Looking for: barang_masuk_dari_produksi\n";
echo "Table exists: " . (in_array('barang_masuk_dari_produksi', $tableNames) ? 'YES' : 'NO') . "\n\n";

if (in_array('barang_masuk_dari_produksi', $tableNames)) {
    echo "=== Table Columns ===\n";
    $result = $db->query("DESCRIBE barang_masuk_dari_produksi")->getResultArray();
    foreach ($result as $col) {
        echo "- " . $col['Field'] . " (" . $col['Type'] . ") " . ($col['Null'] == 'NO' ? 'NOT NULL' : 'nullable') . "\n";
    }
}

// Test model insert
echo "\n=== Testing Model Insert ===\n";
$model = new \App\Models\MBarangMasukDariProduksi();

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

echo "Test data: " . json_encode($testData) . "\n";

try {
    $insert_id = $model->insert($testData);
    echo "Insert successful! ID: " . $insert_id . "\n";

    // Try to retrieve it
    $saved = $model->find($insert_id);
    echo "Retrieved: " . json_encode($saved) . "\n";

    // Delete test data
    $model->delete($insert_id);
    echo "Test data deleted.\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}
