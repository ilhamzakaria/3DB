<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$tables = ['bahan_baku', 'packaging', 'masterbatch', 'stiker_gudang', 'reject_produksi', 'galon_reject', 'gilingan_galon'];
foreach($tables as $t) {
    $r = $mysqli->query("SELECT * FROM $t LIMIT 1");
    if($r) {
        if($r->num_rows > 0) {
            echo "Table $t has data. First row:\n";
            print_r($r->fetch_assoc());
        } else {
            echo "Table $t is empty.\n";
        }
    } else {
        echo "Table $t does not exist.\n";
    }
}
$mysqli->close();
?>
