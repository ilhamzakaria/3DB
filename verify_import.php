<?php
/**
 * Finish Goods Import Verification Script
 * 
 * Verify that all finish goods were imported correctly into the database.
 * 
 * Usage: php verify_import.php
 */

require 'vendor/autoload.php';
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require_once 'system/Test/bootstrap.php';

use App\Models\MFinishGoodGalon;
use App\Models\MFinishGoodCapGalon;
use App\Models\MFinishGoodFreeform;
use App\Models\MFinishGoodScrewCap;

echo "=== Import Verification ===\n\n";

$galonModel = new MFinishGoodGalon();
$capGalonModel = new MFinishGoodCapGalon();
$freeformModel = new MFinishGoodFreeform();
$screwCapModel = new MFinishGoodScrewCap();

// Count records
$galonCount = $galonModel->countAll();
$capGalonCount = $capGalonModel->countAll();
$freeformCount = $freeformModel->countAll();
$screwCapCount = $screwCapModel->countAll();

echo "Gallon: $galonCount records\n";
echo "Cap Gallon: $capGalonCount records\n";
echo "Freeform: $freeformCount records\n";
echo "Screw Cap: $screwCapCount records\n";
echo "Total: " . ($galonCount + $capGalonCount + $freeformCount + $screwCapCount) . " records\n\n";

// Show sample from each table
echo "=== Sample Records ===\n\n";

if ($galonCount > 0) {
    $sample = $galonModel->first();
    echo "Gallon Sample:\n";
    echo "  - Nomor: " . $sample['nomor'] . "\n";
    echo "  - Nama Produk: " . $sample['nama_produk'] . "\n";
    echo "  - Grade: " . $sample['grade'] . "\n";
    echo "  - Berat: " . $sample['berat'] . "\n";
    echo "  - WH ORO: " . ($sample['wh_oro'] ?? 'N/A') . "\n";
    echo "  - WH BAM: " . ($sample['wh_bam'] ?? 'N/A') . "\n\n";
}

if ($capGalonCount > 0) {
    $sample = $capGalonModel->first();
    echo "Cap Gallon Sample:\n";
    echo "  - Nomor: " . $sample['nomor'] . "\n";
    echo "  - Nama Produk: " . $sample['nama_produk'] . "\n";
    echo "  - Warna: " . $sample['warna'] . "\n";
    echo "  - Berat: " . $sample['berat'] . "\n";
    echo "  - Jumlah: " . $sample['jumlah'] . "\n\n";
}

if ($freeformCount > 0) {
    $sample = $freeformModel->first();
    echo "Freeform Sample:\n";
    echo "  - Nomor: " . $sample['nomor'] . "\n";
    echo "  - Nama Produk: " . $sample['nama_produk'] . "\n";
    echo "  - Warna: " . $sample['warna'] . "\n";
    echo "  - Berat: " . $sample['berat'] . "\n";
    echo "  - Jumlah: " . $sample['jumlah'] . "\n\n";
}

if ($screwCapCount > 0) {
    $sample = $screwCapModel->first();
    echo "Screw Cap Sample:\n";
    echo "  - Nomor: " . $sample['nomor'] . "\n";
    echo "  - Nama Produk: " . $sample['nama_produk'] . "\n";
    echo "  - Warna: " . $sample['warna'] . "\n";
    echo "  - Berat: " . $sample['berat'] . "\n";
    echo "  - Jumlah: " . ($sample['jumlah'] ?? 'N/A') . "\n";
    echo "  - Keterangan: " . ($sample['keterangan'] ?? 'N/A') . "\n\n";
}

echo "=== Verification Complete ===\n";
