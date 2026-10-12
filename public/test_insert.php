<?php
// Simple test page accessible via browser
require_once __DIR__ . '/../vendor/autoload.php';

// Initialize CodeIgniter
\Config\Services::autoloader()->addNamespace('Config', rtrim(realpath(APPPATH . 'Config'), '/') . '/');

try {
    $db = \Config\Database::connect('master');
    
    echo "<h2>Database Test Insert</h2>";
    
    // Test data
    $test_data = [
        'id_user' => 'test_' . time(),
        'product' => 'Test Produk ' . date('Y-m-d H:i:s'),
        'machine' => 'IPS 1',
        'type' => 'HDPE',
        'no_mc' => 'TEST-' . time(),
        'tanggal' => date('Y-m-d'),
        'shift' => 1,
    ];
    
    echo "<p><strong>Inserting data:</strong></p>";
    echo "<pre>";
    print_r($test_data);
    echo "</pre>";
    
    // Insert
    $result = $db->table('setting_mesin')->insert($test_data);
    $insertId = $db->insertID();
    
    echo "<p><strong>Result:</strong> " . ($result ? "✓ SUCCESS (ID: $insertId)" : "✗ FAILED") . "</p>";
    
    // Check total
    $total = $db->table('setting_mesin')->countAll();
    echo "<p><strong>Total records in database:</strong> $total</p>";
    
    // Show last 5
    echo "<p><strong>Last 5 records:</strong></p>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>ID</th><th>User</th><th>Product</th><th>Machine</th><th>Tanggal</th></tr>";
    
    $records = $db->table('setting_mesin')->orderBy('id', 'DESC')->limit(5)->get()->getResultArray();
    foreach ($records as $r) {
        echo "<tr>";
        echo "<td>" . $r['id'] . "</td>";
        echo "<td>" . $r['id_user'] . "</td>";
        echo "<td>" . $r['product'] . "</td>";
        echo "<td>" . $r['machine'] . "</td>";
        echo "<td>" . $r['tanggal'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
} catch (\Exception $e) {
    echo "<h2>Error</h2>";
    echo "<p><strong style='color:red;'>" . $e->getMessage() . "</strong></p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
