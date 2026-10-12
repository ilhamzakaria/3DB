<?php
// Test direct insert to database
require_once 'vendor/autoload.php';

$config = new Config\Database();
$forge = \Config\Database::connect('master');

$data = [
    'id_user' => 'test_operator',
    'product' => 'Test Produk',
    'machine' => 'Test Mesin IPS 1',
    'type' => 'Test Type',
    'no_mc' => 'MC-2026-001',
    'tanggal' => date('Y-m-d'),
    'shift' => 1,
];

$result = $forge->table('setting_mesin')->insert($data);

echo "Insert result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
echo "Last ID: " . $forge->insertID() . "\n";

// Check if data was inserted
$records = $forge->table('setting_mesin')->get()->getResultArray();
echo "Total records now: " . count($records) . "\n";
echo "Last record:\n";
if (!empty($records)) {
    $last = end($records);
    print_r($last);
}
?>
