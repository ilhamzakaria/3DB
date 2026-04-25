<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    echo $row[0] . "\n";
}
$mysqli->close();
?>
