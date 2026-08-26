<?php
require 'vendor/autoload.php';
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$app = require_once 'app/Config/Paths.php';
$config = new Config\Paths();
require_once 'system/Test/bootstrap.php';

$db = \Config\Database::connect('gudang');
try {
    echo "Adding tipe column...\n";
    $db->query("ALTER TABLE bahan_baku ADD COLUMN tipe VARCHAR(255) NULL AFTER produk");
    echo "Done!\n";
} catch (\Exception $e) {
    echo "Error (might already exist): " . $e->getMessage() . "\n";
}
