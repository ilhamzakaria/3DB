<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class TestDb extends Controller
{
    public function index()
    {
        $db = \Config\Database::connect();
        $query = $db->query("SELECT * FROM gudang LIMIT 5");
        $results = $query->getResultArray();
        echo "Gudang:\n";
        print_r($results);

        try {
            $query2 = $db->query("SELECT * FROM gudang_item_details LIMIT 5");
            $results2 = $query2->getResultArray();
            echo "\nGudang Details:\n";
            print_r($results2);
        } catch (\Exception $e) {
            echo "\nError fetching gudang details: " . $e->getMessage();
        }
    }
}
