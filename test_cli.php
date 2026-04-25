<?php
// Define paths
define('FCPATH', __DIR__ . DIRECTORY_SEPARATOR);
require FCPATH . 'app/Config/Paths.php';
$paths = new \Config\Paths();
define('APPPATH', rtrim($paths->appDirectory, '\\/ ') . DIRECTORY_SEPARATOR);
define('ROOTPATH', rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR . '../');
define('SYSTEMPATH', rtrim($paths->systemDirectory, '\\/ ') . DIRECTORY_SEPARATOR);
define('WRITEPATH', rtrim($paths->writableDirectory, '\\/ ') . DIRECTORY_SEPARATOR);

require SYSTEMPATH . 'bootstrap.php';
$db = \Config\Database::connect();
$result = $db->query("SELECT * FROM gudang LIMIT 5")->getResultArray();
echo "Gudang rows: " . count($result) . "\n";
print_r($result);
