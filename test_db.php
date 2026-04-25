<?php
$mysqli = new mysqli("localhost", "root", "root", "3d");
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
$result = $mysqli->query("SELECT * FROM gudang LIMIT 5");
if ($result->num_rows > 0) {
    echo "gudang: " . $result->num_rows . " rows\n";
    print_r($result->fetch_assoc());
} else {
    echo "0 results in gudang table\n";
}
$result2 = $mysqli->query("SELECT * FROM gudang_item_details LIMIT 5");
if ($result2->num_rows > 0) {
    echo "gudang_item_details: " . $result2->num_rows . " rows\n";
    print_r($result2->fetch_assoc());
} else {
    echo "0 results in gudang_item_details table\n";
}
$result3 = $mysqli->query("SELECT COUNT(*) as cnt FROM gudang_item_details");
$row3 = $result3->fetch_assoc();
echo "total in gudang_item_details: " . $row3['cnt'] . "\n";
$mysqli->close();
?>
