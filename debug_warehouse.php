<?php
/**
 * Debug Script untuk Warehouse2 Error 500
 * 
 * CARA PAKAI:
 * 1. Upload file ini ke root project di hosting (sejajar dengan index.php)
 * 2. Buka di browser: https://oro3departemen.my.id/debug_warehouse.php
 * 3. Lihat output untuk identifikasi masalah
 * 4. HAPUS file ini setelah selesai debug (jangan dibiarkan di production)
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔍 Warehouse2 Debugging Tool</h1>";
echo "<style>body{font-family:monospace;padding:20px;background:#f5f5f5} .ok{color:green} .error{color:red} .warning{color:orange}</style>";

// 1. Check PHP Version
echo "<h2>1️⃣ PHP Environment</h2>";
echo "<strong>PHP Version:</strong> " . phpversion();
if (version_compare(phpversion(), '8.1.0', '>=')) {
    echo " <span class='ok'>✓ OK (>= 8.1)</span><br>";
} else {
    echo " <span class='error'>✗ FAIL (Need >= 8.1)</span><br>";
}

// 2. Check Required Extensions
echo "<h2>2️⃣ PHP Extensions</h2>";
$required = ['mysqli', 'json', 'mbstring', 'intl'];
foreach ($required as $ext) {
    echo "<strong>$ext:</strong> ";
    if (extension_loaded($ext)) {
        echo "<span class='ok'>✓ Loaded</span><br>";
    } else {
        echo "<span class='error'>✗ NOT LOADED</span><br>";
    }
}

// 3. Check .env file
echo "<h2>3️⃣ Environment File</h2>";
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    echo "<strong>.env file:</strong> <span class='ok'>✓ EXISTS</span><br>";
    
    // Parse .env untuk warehouse config
    $envContent = file_get_contents($envFile);
    $hasWarehouseDB = strpos($envContent, 'database.warehouse.database') !== false;
    $hasWarehouseUser = strpos($envContent, 'database.warehouse.username') !== false;
    $hasWarehousePass = strpos($envContent, 'database.warehouse.password') !== false;
    
    echo "<strong>Warehouse DB Config:</strong> ";
    if ($hasWarehouseDB && $hasWarehouseUser && $hasWarehousePass) {
        echo "<span class='ok'>✓ FOUND</span><br>";
    } else {
        echo "<span class='error'>✗ MISSING atau INCOMPLETE</span><br>";
        echo "<small>Pastikan ada: database.warehouse.database, database.warehouse.username, database.warehouse.password</small><br>";
    }
} else {
    echo "<strong>.env file:</strong> <span class='error'>✗ NOT FOUND</span><br>";
    echo "<small>Copy file .env.hosting dan rename jadi .env</small><br>";
}

// 4. Check writable folder
echo "<h2>4️⃣ File Permissions</h2>";
$writable = __DIR__ . '/writable';
if (is_dir($writable)) {
    echo "<strong>writable/ folder:</strong> <span class='ok'>✓ EXISTS</span><br>";
    if (is_writable($writable)) {
        echo "<strong>writable/ permission:</strong> <span class='ok'>✓ WRITABLE</span><br>";
    } else {
        echo "<strong>writable/ permission:</strong> <span class='error'>✗ NOT WRITABLE</span><br>";
        echo "<small>Jalankan: chmod 755 writable/ -R</small><br>";
    }
    
    // Check logs folder
    $logs = $writable . '/logs';
    if (is_dir($logs) && is_writable($logs)) {
        echo "<strong>writable/logs/:</strong> <span class='ok'>✓ WRITABLE</span><br>";
        
        // Show latest log file
        $logFiles = glob($logs . '/log-*.php');
        if (!empty($logFiles)) {
            rsort($logFiles); // Latest first
            $latestLog = basename($logFiles[0]);
            echo "<strong>Latest log:</strong> $latestLog<br>";
            echo "<small>Check isi file ini untuk detail error</small><br>";
        } else {
            echo "<strong>Log files:</strong> <span class='warning'>⚠ NONE FOUND</span><br>";
        }
    } else {
        echo "<strong>writable/logs/:</strong> <span class='error'>✗ NOT WRITABLE</span><br>";
    }
} else {
    echo "<strong>writable/ folder:</strong> <span class='error'>✗ NOT FOUND</span><br>";
}

// 5. Test Database Connection (WITHOUT loading CodeIgniter)
echo "<h2>5️⃣ Database Connection Test</h2>";

// Parse .env manually untuk test
if (file_exists($envFile)) {
    $env = parse_ini_file($envFile);
    
    $dbHost = $env['database_warehouse_hostname'] ?? 'localhost';
    $dbName = $env['database_warehouse_database'] ?? '';
    $dbUser = $env['database_warehouse_username'] ?? '';
    $dbPass = $env['database_warehouse_password'] ?? '';
    
    if (empty($dbName) || empty($dbUser)) {
        echo "<span class='error'>✗ Database warehouse config tidak lengkap di .env</span><br>";
    } else {
        echo "<strong>Testing connection to:</strong> $dbUser@$dbHost/$dbName<br>";
        
        // Try connect
        $mysqli = @new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        
        if ($mysqli->connect_error) {
            echo "<span class='error'>✗ CONNECTION FAILED</span><br>";
            echo "<strong>Error:</strong> " . $mysqli->connect_error . "<br>";
            echo "<small>Pastikan username, password, dan database name benar di .env</small><br>";
        } else {
            echo "<span class='ok'>✓ CONNECTION SUCCESS</span><br>";
            
            // Check if table exists
            $result = $mysqli->query("SHOW TABLES LIKE 'orowarehouse'");
            if ($result && $result->num_rows > 0) {
                echo "<strong>Table 'orowarehouse':</strong> <span class='ok'>✓ EXISTS</span><br>";
                
                // Check table structure
                $columns = $mysqli->query("SHOW COLUMNS FROM orowarehouse");
                $columnNames = [];
                while ($row = $columns->fetch_assoc()) {
                    $columnNames[] = $row['Field'];
                }
                
                $requiredColumns = ['id', 'tanggal', 'spk', 'produk', 'shift', 'hasil_bagus', 'hasil_reject', 
                                   'material', 'colorant', 'packaging', 'operator', 'status', 'created_at', 'updated_at', 'deleted_at'];
                $missingColumns = array_diff($requiredColumns, $columnNames);
                
                if (empty($missingColumns)) {
                    echo "<strong>Table structure:</strong> <span class='ok'>✓ COMPLETE</span><br>";
                    echo "<small>Columns: " . implode(', ', $columnNames) . "</small><br>";
                } else {
                    echo "<strong>Table structure:</strong> <span class='error'>✗ MISSING COLUMNS</span><br>";
                    echo "<strong>Missing:</strong> " . implode(', ', $missingColumns) . "<br>";
                    echo "<small>Run migration: php spark migrate --group warehouse</small><br>";
                }
            } else {
                echo "<strong>Table 'orowarehouse':</strong> <span class='error'>✗ NOT FOUND</span><br>";
                echo "<small>Run migration: php spark migrate --group warehouse</small><br>";
            }
            
            $mysqli->close();
        }
    }
} else {
    echo "<span class='error'>✗ Cannot test - .env file not found</span><br>";
}

// 6. Summary
echo "<h2>📝 Summary & Next Steps</h2>";
echo "<ol>";
echo "<li>Jika ada tanda <span class='error'>✗ merah</span>, perbaiki issue tersebut terlebih dahulu</li>";
echo "<li>Cek file log di writable/logs/ untuk detail error message</li>";
echo "<li>Pastikan semua config database.warehouse.* ada di file .env</li>";
echo "<li>Jalankan migration jika table belum ada: <code>php spark migrate --group warehouse</code></li>";
echo "<li><strong>HAPUS file debug_warehouse.php ini setelah selesai!</strong></li>";
echo "</ol>";

echo "<hr>";
echo "<p><small>Generated: " . date('Y-m-d H:i:s') . "</small></p>";
