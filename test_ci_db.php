<?php
require 'vendor/autoload.php';
$app = require_once 'system/bootstrap.php';
$db = \Config\Database::connect();
$query = $db->query("SELECT * FROM gudang LIMIT 5");
$results = $query->getResultArray();
echo "Gudang:\n";
print_r($results);

$query2 = $db->query("SELECT * FROM gudang_item_details LIMIT 5");
$results2 = $query2->getResultArray();
echo "\nGudang Details:\n";
print_r($results2);
?>
