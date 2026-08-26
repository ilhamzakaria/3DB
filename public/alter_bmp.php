<?php
/**
 * Script untuk alter table barang_masuk_produksi - tambah kolom detail produksi
 * Jalankan sekali di: http://localhost:8080/alter_bmp.php
 */

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'orowarehouse';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $results = [];

    // Cek kolom yang sudah ada
    $existing = [];
    $stmt = $pdo->query("SHOW COLUMNS FROM `barang_masuk_produksi`");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        $existing[] = $col['Field'];
    }

    echo "<h2>Kolom yang sudah ada di barang_masuk_produksi:</h2>";
    echo "<pre>" . implode(', ', $existing) . "</pre>";

    $alters = [];

    if (!in_array('spk', $existing)) {
        $alters[] = "ADD COLUMN `spk` VARCHAR(50) NULL AFTER `no_dokumen`";
    }
    if (!in_array('produk', $existing)) {
        $alters[] = "ADD COLUMN `produk` VARCHAR(255) NULL AFTER `spk`";
    }
    if (!in_array('shift', $existing)) {
        $alters[] = "ADD COLUMN `shift` ENUM('1','2','3') NULL AFTER `produk`";
    }
    if (!in_array('grup', $existing)) {
        $alters[] = "ADD COLUMN `grup` VARCHAR(100) NULL AFTER `shift`";
    }
    if (!in_array('hasil_bagus', $existing)) {
        $alters[] = "ADD COLUMN `hasil_bagus` DECIMAL(12,2) NULL DEFAULT 0 AFTER `grup`";
    }
    if (!in_array('hasil_reject', $existing)) {
        $alters[] = "ADD COLUMN `hasil_reject` DECIMAL(12,2) NULL DEFAULT 0 AFTER `hasil_bagus`";
    }
    if (!in_array('material', $existing)) {
        $alters[] = "ADD COLUMN `material` VARCHAR(255) NULL AFTER `hasil_reject`";
    }
    if (!in_array('colorant', $existing)) {
        $alters[] = "ADD COLUMN `colorant` VARCHAR(255) NULL AFTER `material`";
    }
    if (!in_array('packaging', $existing)) {
        $alters[] = "ADD COLUMN `packaging` VARCHAR(255) NULL AFTER `colorant`";
    }
    if (!in_array('operator', $existing)) {
        $alters[] = "ADD COLUMN `operator` VARCHAR(100) NULL AFTER `packaging`";
    }

    if (empty($alters)) {
        echo "<p style='color:green'><b>✅ Semua kolom sudah ada! Tidak perlu alter.</b></p>";
    } else {
        $sql = "ALTER TABLE `barang_masuk_produksi` " . implode(", \n", $alters);
        echo "<h3>SQL yang akan dijalankan:</h3><pre>" . htmlspecialchars($sql) . "</pre>";
        
        $pdo->exec($sql);
        echo "<p style='color:green'><b>✅ ALTER TABLE berhasil! Kolom ditambahkan.</b></p>";
    }

    // Tambah index jika belum ada
    $indexes = [];
    $stmt = $pdo->query("SHOW INDEX FROM `barang_masuk_produksi`");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $idx) {
        $indexes[] = $idx['Key_name'];
    }

    if (!in_array('idx_bmp_spk', $indexes)) {
        try { $pdo->exec("ALTER TABLE `barang_masuk_produksi` ADD INDEX `idx_bmp_spk` (`spk`)"); echo "<p>✅ Index idx_bmp_spk ditambahkan</p>"; }
        catch(Exception $e) { echo "<p>Index idx_bmp_spk: " . $e->getMessage() . "</p>"; }
    }
    if (!in_array('idx_bmp_status', $indexes)) {
        try { $pdo->exec("ALTER TABLE `barang_masuk_produksi` ADD INDEX `idx_bmp_status` (`status`)"); echo "<p>✅ Index idx_bmp_status ditambahkan</p>"; }
        catch(Exception $e) { echo "<p>Index idx_bmp_status: " . $e->getMessage() . "</p>"; }
    }

    // Tampilkan struktur akhir
    echo "<h3>Struktur tabel setelah alter:</h3><table border='1' cellpadding='5'><tr><th>Field</th><th>Type</th><th>Null</th><th>Default</th></tr>";
    $stmt = $pdo->query("SHOW COLUMNS FROM `barang_masuk_produksi`");
    foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $col) {
        echo "<tr><td>{$col['Field']}</td><td>{$col['Type']}</td><td>{$col['Null']}</td><td>{$col['Default']}</td></tr>";
    }
    echo "</table>";

    echo "<br><p style='color:blue'><b>Selesai! Silakan hapus file ini setelah selesai.</b></p>";

} catch (Exception $e) {
    echo "<p style='color:red'><b>Error: " . $e->getMessage() . "</b></p>";
}
