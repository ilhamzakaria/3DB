<?php
/**
 * Run Migration Script - HAPUS FILE INI SETELAH DIPAKAI!
 * Akses: https://yourdomain.com/run_migrate.php
 */

// Proteksi sederhana - ganti password ini
$password = $_GET['key'] ?? '';
if ($password !== 'rahasia123') {
    die('Access denied. Tambahkan ?key=rahasia123 di URL');
}

// Bootstrap CodeIgniter
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR);
chdir(__DIR__);

require_once 'vendor/autoload.php';
require_once 'system/bootstrap.php';

$app = Config\Services::codeigniter();
$app->initialize();

// Jalankan migration
$runner = \Config\Services::migrations();

echo '<pre>';
echo "=== Running Migrations ===\n\n";

try {
    $result = $runner->latest();
    if ($result) {
        echo "✅ Migrations complete!\n";
    } else {
        $error = $runner->error();
        echo "❌ Migration failed: " . ($error ?: 'Unknown error') . "\n";
    }
} catch (\Exception $e) {
    echo "❌ Exception: " . $e->getMessage() . "\n";
}

echo "\n=== History ===\n";
$history = $runner->getHistory();
foreach ($history as $row) {
    echo "✔ " . $row->class . " (batch: " . $row->batch . ")\n";
}

echo "\n⚠️  HAPUS FILE INI SEKARANG!\n";
echo '</pre>';
