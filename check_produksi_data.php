<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
$res = $mysqli->query("SELECT COUNT(*) as count FROM produksi");
$row = $res->fetch_assoc();
echo "Count: " . $row['count'] . "\n";

$res = $mysqli->query("SELECT no_spk, nama_mesin, nama_produk FROM produksi LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
$mysqli->close();
?>
