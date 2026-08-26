#!/usr/bin/env php
<?php
// Drop and recreate the barang_masuk_dari_produksi table with correct schema

$warehouse_config = [
    'host' => 'localhost',
    'username' => 'root',
    'password' => 'root',
    'database' => 'orowarehouse'
];

$mysqli = new mysqli(
    $warehouse_config['host'],
    $warehouse_config['username'],
    $warehouse_config['password'],
    $warehouse_config['database']
);

if ($mysqli->connect_error) {
    echo "Connection failed: " . $mysqli->connect_error . "\n";
    exit(1);
}

echo "Dropping incorrect barang_masuk_dari_produksi table...\n";
$mysqli->query("DROP TABLE IF EXISTS barang_masuk_dari_produksi");

echo "Creating barang_masuk_dari_produksi table with correct schema...\n";

$sql = "CREATE TABLE barang_masuk_dari_produksi (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    no_dokumen VARCHAR(50) NOT NULL UNIQUE,
    tanggal DATE NOT NULL,
    no_spk VARCHAR(50) NOT NULL,
    nama_produksi VARCHAR(255) NOT NULL,
    kode_barang VARCHAR(50) NOT NULL,
    nama_barang VARCHAR(255) NOT NULL,
    satuan VARCHAR(50) NOT NULL,
    jumlah_produksi DECIMAL(12,2) NOT NULL,
    jumlah_reject DECIMAL(12,2) DEFAULT 0,
    jumlah_diterima DECIMAL(12,2) NOT NULL,
    nama_penerima VARCHAR(255),
    catatan TEXT,
    status ENUM('pending', 'diterima', 'ditolak') DEFAULT 'pending',
    created_at DATETIME,
    updated_at DATETIME,
    deleted_at DATETIME,
    INDEX idx_no_dokumen (no_dokumen),
    INDEX idx_no_spk (no_spk),
    INDEX idx_tanggal (tanggal),
    INDEX idx_status (status)
)";

if ($mysqli->query($sql)) {
    echo "✓ Table created successfully\n";

    // Verify
    echo "\nVerifying table columns:\n";
    $result = $mysqli->query("DESCRIBE barang_masuk_dari_produksi");
    while ($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "✗ Error creating table: " . $mysqli->error . "\n";
    exit(1);
}

$mysqli->close();
echo "\n✓ Done!\n";
?>