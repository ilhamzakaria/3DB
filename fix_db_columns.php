<?php
require 'vendor/autoload.php';
// Define constant to bypass CI check
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
$app = require_once 'app/Config/Paths.php';
$config = new Config\Paths();
require_once 'system/Test/bootstrap.php';

$db = \Config\Database::connect('produksi');
try {
    echo "Adding columns to produksi_packagings...\n";
    $db->query("ALTER TABLE produksi_packagings ADD COLUMN box INT DEFAULT 0 AFTER produksi_header_id");
    $db->query("ALTER TABLE produksi_packagings ADD COLUMN box_ukuran VARCHAR(100) NULL AFTER box");
    $db->query("ALTER TABLE produksi_packagings ADD COLUMN plastik_ukuran VARCHAR(100) NULL AFTER plastik");
    $db->query("ALTER TABLE produksi_packagings ADD COLUMN karung INT DEFAULT 0 AFTER plastik_ukuran");
    $db->query("ALTER TABLE produksi_packagings ADD COLUMN karung_ukuran VARCHAR(100) NULL AFTER karung");
    echo "Done!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
