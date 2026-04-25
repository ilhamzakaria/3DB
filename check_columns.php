<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$tables = ['galon_reject', 'gilingan_galon'];
foreach($tables as $t) {
    $res = $mysqli->query("DESCRIBE $t");
    echo "Columns for $t:\n";
    while($row = $res->fetch_assoc()) {
        echo $row['Field'] . "\n";
    }
    echo "\n";
}
$mysqli->close();
?>
