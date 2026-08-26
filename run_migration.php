<?php
/**
 * TEMPORARY FILE - Delete after migration success!
 * 
 * Purpose: Run database migration for produksi group
 * Usage: Upload to hosting root, access via browser, then DELETE
 * URL: https://oro3departemen.my.id/run_migration.php
 */

// Load CodeIgniter
require 'vendor/autoload.php';

$pathsConfig = APPPATH . 'Config/Paths.php';
require $pathsConfig;

$paths = new Config\Paths();
$bootstrap = rtrim($paths->systemDirectory, '\\/ ') . '/bootstrap.php';
$app = require realpath($bootstrap) ?: $bootstrap;

// Initialize services
$migrate = \Config\Services::migrations();
$migrate->setGroup('produksi');

?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Migration - Produksi</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 800px; 
            margin: 50px auto; 
            padding: 20px;
            background: #f5f5f5;
        }
        .box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { 
            color: #155724; 
            background: #d4edda; 
            border: 1px solid #c3e6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .error { 
            color: #721c24; 
            background: #f8d7da; 
            border: 1px solid #f5c6cb;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .warning {
            color: #856404;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
        code {
            background: #f4f4f4;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .btn:hover {
            background: #0056b3;
        }
        h1 { color: #333; }
        hr { margin: 30px 0; border: 0; border-top: 1px solid #ddd; }
    </style>
</head>
<body>
    <div class="box">
        <h1>🔧 Database Migration Runner</h1>
        <p><strong>Group:</strong> produksi</p>
        <p><strong>Purpose:</strong> Add <code>auto_user_ttd</code> field to <code>produksi_headers</code></p>
        
        <hr>
        
        <?php
        try {
            // Run migration
            $result = $migrate->latest();
            
            if ($result === false) {
                $messages = $migrate->getCliMessages();
                echo '<div class="error">';
                echo '<strong>❌ Migration GAGAL</strong><br><br>';
                echo 'Error details:<br>';
                echo '<pre>' . htmlspecialchars(print_r($messages, true)) . '</pre>';
                echo '</div>';
                
                echo '<div class="warning">';
                echo '<strong>Solusi Alternatif:</strong><br>';
                echo 'Jalankan query manual di phpMyAdmin:<br><br>';
                echo '<code>ALTER TABLE `produksi_headers` ADD COLUMN `auto_user_ttd` VARCHAR(255) NULL AFTER `ttd_spv`;</code>';
                echo '</div>';
            } else {
                echo '<div class="success">';
                echo '<strong>✅ Migration BERHASIL!</strong><br><br>';
                echo 'Field <code>auto_user_ttd</code> berhasil ditambahkan ke table <code>produksi_headers</code>.<br><br>';
                echo '<strong>Next steps:</strong><br>';
                echo '1. <strong style="color: #d63031;">HAPUS file ini (run_migration.php) dari server!</strong><br>';
                echo '2. Test tambah data produksi → seharusnya sudah berhasil<br>';
                echo '3. Jika masih error, cek error message yang lebih detail sekarang muncul';
                echo '</div>';
                
                // Show CLI messages if any
                $messages = $migrate->getCliMessages();
                if (!empty($messages)) {
                    echo '<details>';
                    echo '<summary>Migration Details</summary>';
                    echo '<pre>' . htmlspecialchars(print_r($messages, true)) . '</pre>';
                    echo '</details>';
                }
            }
        } catch (\Throwable $e) {
            echo '<div class="error">';
            echo '<strong>❌ ERROR EXCEPTION</strong><br><br>';
            echo '<strong>Message:</strong> ' . htmlspecialchars($e->getMessage()) . '<br>';
            echo '<strong>File:</strong> ' . htmlspecialchars($e->getFile()) . '<br>';
            echo '<strong>Line:</strong> ' . $e->getLine() . '<br><br>';
            echo '<details>';
            echo '<summary>Stack Trace</summary>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</details>';
            echo '</div>';
            
            echo '<div class="warning">';
            echo '<strong>Solusi Alternatif:</strong><br>';
            echo 'Jalankan query manual di phpMyAdmin (database <code>oroproduksi</code>):<br><br>';
            echo '<code>ALTER TABLE `produksi_headers` ADD COLUMN `auto_user_ttd` VARCHAR(255) NULL AFTER `ttd_spv`;</code>';
            echo '</div>';
        }
        ?>
        
        <hr>
        
        <div class="warning">
            <strong>⚠️ PENTING - SECURITY WARNING!</strong><br>
            File ini memberikan akses ke database migration.<br>
            <strong>HAPUS file ini segera setelah selesai!</strong>
        </div>
        
        <a href="/" class="btn">← Kembali ke Dashboard</a>
    </div>
</body>
</html>
