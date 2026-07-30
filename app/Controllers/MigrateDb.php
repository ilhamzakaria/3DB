<?php
namespace App\Controllers;
use CodeIgniter\Controller;

class MigrateDb extends Controller
{
    public function addJumlah()
    {
        $db = \Config\Database::connect('ppic');
        try {
            $db->query("ALTER TABLE ppic ADD COLUMN jumlah VARCHAR(255) NULL");
            echo "Column 'jumlah' added successfully to 'ppic' table in 'oroppic' database.";
        } catch (\Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
