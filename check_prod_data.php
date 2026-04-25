<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
$res = $mysqli->query("SELECT COUNT(*) as count FROM prod");
$row = $res->fetch_assoc();
echo "Prod Count: " . $row['count'] . "\n";

$res = $mysqli->query("SELECT no_spk, nama_mesin, nama_produksi FROM prod LIMIT 5");
while($row = $res->fetch_assoc()) {
    print_r($row);
}
$mysqli->close();
?>
