<?php
/**
 * Test script untuk memverifikasi bahwa PPIC tidak bisa akses warehouse2 CRUD operations
 */

session_start();

// Simulasi user PPIC login
$_SESSION['logged_in'] = true;
$_SESSION['role'] = 'ppic';
$_SESSION['user_id'] = 123;
$_SESSION['username'] = 'test_ppic';

echo "=== TESTING PPIC ACCESS TO WAREHOUSE2 ===\n";
echo "Current session role: " . ($_SESSION['role'] ?? 'not set') . "\n\n";

// Test URLs yang seharusnya di-block untuk PPIC
$test_urls = [
    'warehouse2/store-pemasok',
    'warehouse2/delete-pemasok/1', 
    'warehouse2/store-produksi',
    'warehouse2/delete-produksi/1',
    'warehouse2/store-pembeli', 
    'warehouse2/delete-pembeli/1',
    'warehouse2/store-orowarehouse',
    'warehouse2/update-orowarehouse/1',
    'warehouse2/delete-orowarehouse/1'
];

foreach ($test_urls as $url) {
    echo "Testing URL: $url\n";
    
    // Simulasi test curl request
    $full_url = "http://localhost:8080/$url";
    
    // Test dengan session PPIC
    $context = stream_context_create([
        'http' => [
            'method' => strpos($url, 'delete') !== false ? 'DELETE' : 'POST',
            'header' => [
                'Content-Type: application/json',
                'X-Requested-With: XMLHttpRequest',
                'Cookie: ci_session=' . session_id()
            ],
            'content' => json_encode(['test' => 'data']),
            'ignore_errors' => true
        ]
    ]);
    
    $response = @file_get_contents($full_url, false, $context);
    
    // Parse HTTP response code
    if (isset($http_response_header)) {
        $status_line = $http_response_header[0];
        preg_match('/HTTP\/\S+\s+(\d+)/', $status_line, $matches);
        $status_code = $matches[1] ?? 'unknown';
        
        echo "Response Code: $status_code\n";
        
        if ($status_code == '403') {
            echo "✅ BLOCKED - Access correctly denied\n";
        } else if ($status_code == '200' || $status_code == '201') {
            echo "❌ ALLOWED - Security vulnerability! PPIC can access this endpoint\n";
        } else {
            echo "ℹ️  OTHER - Code $status_code (might be method not allowed, which is expected)\n";
        }
        
        if ($response) {
            $decoded = json_decode($response, true);
            if ($decoded && isset($decoded['message'])) {
                echo "Message: " . $decoded['message'] . "\n";
            }
        }
    }
    
    echo "---\n";
}

echo "\n=== TEST COMPLETED ===\n";
echo "Note: 403 responses indicate proper access control.\n";
echo "200/201 responses indicate potential security issues.\n";
?>