<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
$res = $mysqli->query("DESCRIBE produksi");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
$mysqli->close();
?>
