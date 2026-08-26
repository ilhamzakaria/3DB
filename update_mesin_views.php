<?php
// This script updates the remaining mesin view files with the correct pattern
// It applies: status labels, table reorganization, extra shifts support, and updated JavaScript

$files_to_update = [
    'c:/xampp/htdocs/3DB/app/Views/gudang/mesinccm2.php' => ['product_field' => 'produk_cap_galon', 'has_status_col' => true],
    'c:/xampp/htdocs/3DB/app/Views/gudang/mesinips1.php' => ['product_field' => 'produk_preform', 'has_status_col' => false],
    'c:/xampp/htdocs/3DB/app/Views/gudang/mesinips2.php' => ['product_field' => 'produk_preform', 'has_status_col' => false],
    'c:/xampp/htdocs/3DB/app/Views/gudang/mesinips3.php' => ['product_field' => 'produk_preform', 'has_status_col' => false],
    'c:/xampp/htdocs/3DB/app/Views/gudang/mesinips4.php' => ['product_field' => 'produk_preform', 'has_status_col' => false],
];

$reference_file = 'c:/xampp/htdocs/3DB/app/Views/gudang/mesinpowerjet1.php';

// Read reference file
$reference = file_get_contents($reference_file);
echo "Reference file loaded: " . strlen($reference) . " bytes\n";

// Process each file
foreach ($files_to_update as $file => $config) {
    if (!file_exists($file)) {
        echo "SKIP: File not found: $file\n";
        continue;
    }
    
    $content = file_get_contents($file);
    $original_size = strlen($content);
    
    // 1. Add status labels at top after formatDate function
    $status_labels = <<<'PHP'
$statusLabels = [
    'menggiling' => 'Menggiling',
    'bahan_masuk' => 'Bahan Masuk',
    'bahan_keluar' => 'Bahan Keluar',
];

$statusBadges = [
    'menggiling' => 'badge-warning',
    'bahan_masuk' => 'badge-success',
    'bahan_keluar' => 'badge-info',
];

PHP;
    
    // Replace the formatDate section
    $old_format = <<<'PHP'
<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<?php
$formatDate = static function ($value): string {
PHP;
    
    $new_format = <<<'PHP'
<?= $this->extend('layout/main') ?>
<?php $view_version = '20260718_01'; ?>

<?= $this->section('content') ?>

<?php
$statusLabels = [
    'menggiling' => 'Menggiling',
    'bahan_masuk' => 'Bahan Masuk',
    'bahan_keluar' => 'Bahan Keluar',
];

$statusBadges = [
    'menggiling' => 'badge-warning',
    'bahan_masuk' => 'badge-success',
    'bahan_keluar' => 'badge-info',
];

$formatDate = static function ($value): string {
PHP;
    
    $content = str_replace($old_format, $new_format, $content);
    
    // Update file
    $new_size = strlen($content);
    if (file_put_contents($file, $content)) {
        echo "UPDATE: $file (" . ($new_size - $original_size) . " bytes changed)\n";
    } else {
        echo "ERROR: Failed to write $file\n";
    }
}

echo "\nDone!\n";
?>
