<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$tables = ['bahan_baku', 'packaging', 'masterbatch', 'stiker_gudang', 'reject_produksi', 'galon_reject', 'gilingan_galon'];
foreach($tables as $t) {
    $res = $mysqli->query("DESCRIBE $t");
    echo "Columns for $t:\n";
    $cols = [];
    while($row = $res->fetch_assoc()) {
        $cols[] = $row['Field'];
    }
    echo implode(", ", $cols) . "\n\n";
}
$mysqli->close();
?>
