<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=oroppic', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $db->exec("ALTER TABLE ppic ADD COLUMN jumlah VARCHAR(255) NULL");
    echo "Column 'jumlah' added to 'ppic' table in 'oroppic' DB.<br>";
} catch (PDOException $e) {
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Column 'jumlah' already exists in 'ppic'.<br>";
    } else {
        echo "Error modifying ppic: " . $e->getMessage() . "<br>";
    }
}
