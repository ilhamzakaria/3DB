<?php
// Database connection test
$db = new mysqli('localhost', 'root', 'root', 'oromaster');

if ($db->connect_error) {
    echo 'Database Error: ' . $db->connect_error;
    die();
}

echo "Database connected successfully.\n\n";

// Check table exists
$result = $db->query('SHOW TABLES LIKE "setting_mesin"');
if ($result->num_rows == 0) {
    echo "Table 'setting_mesin' does not exist!\n";
} else {
    echo "Table 'setting_mesin' exists.\n\n";
}

// Check table structure
echo "Table structure:\n";
$result = $db->query('DESCRIBE setting_mesin');
while ($field = $result->fetch_assoc()) {
    echo $field['Field'] . ' (' . $field['Type'] . ')' . "\n";
}

echo "\n";

// Count records
$result = $db->query('SELECT COUNT(*) as count FROM setting_mesin');
$row = $result->fetch_assoc();
echo "Total records: " . $row['count'] . "\n";

// Show last 5 records
echo "\nLast 5 records:\n";
$result = $db->query('SELECT * FROM setting_mesin ORDER BY id DESC LIMIT 5');
while ($record = $result->fetch_assoc()) {
    echo "ID: " . $record['id'] . " | User: " . $record['id_user'] . " | Product: " . $record['product'] . "\n";
}

$db->close();
?>
