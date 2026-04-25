<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
$res = $mysqli->query("DESCRIBE prod");
while($row = $res->fetch_assoc()) {
    echo $row['Field'] . "\n";
}
$mysqli->close();
?>
