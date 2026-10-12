<?php

namespace App\Controllers;

class DbCheck extends BaseController
{
    public function check()
    {
        // Basic auth guard - only allow in production with a secret key
        if (ENVIRONMENT === 'production') {
            $secret = $this->request->getGet('key');
            if ($secret !== 'debug_oro2025') {
                return $this->response->setStatusCode(403)->setBody('Forbidden');
            }
        }

        $groups = ['default', 'master', 'gudang', 'ppic', 'produksi', 'warehouse'];
        $results = [];

        foreach ($groups as $group) {
            try {
                $db = \Config\Database::connect($group);
                $tables = $db->listTables();
                $results[$group] = [
                    'status'   => 'OK',
                    'database' => $db->getDatabase(),
                    'tables'   => $tables,
                    'count'    => count($tables),
                ];
            } catch (\Exception $e) {
                $results[$group] = [
                    'status'  => 'ERROR',
                    'message' => $e->getMessage(),
                ];
            }
        }

        // Test insert to produksi (dry run - rollback)
        $insertTest = 'NOT TESTED';
        try {
            $db = \Config\Database::connect('produksi');
            $db->transStart();
            $ok = $db->table('produksi_headers')->insert([
                'no_spk'              => 'TEST-DEBUG',
                'nama_mesin'          => 'Test',
                'tanggal'             => date('Y-m-d'),
                'grand_total_bagus'   => 0,
                'grand_total_reject'  => 0,
            ]);
            $insertId = $db->insertID();
            $db->transRollback(); // Always rollback - this is just a test
            $insertTest = $ok ? "Insert OK, ID would be: $insertId" : 'Insert returned false. DB Error: ' . json_encode($db->error());
        } catch (\Exception $e) {
            $insertTest = 'Exception: ' . $e->getMessage();
        }

        $results['insert_test_produksi'] = $insertTest;
        $results['ci_environment']       = ENVIRONMENT;
        $results['php_version']          = PHP_VERSION;
        $results['writable_session']     = is_writable(WRITEPATH . 'session') ? 'WRITABLE' : 'NOT WRITABLE';
        $results['writable_logs']        = is_writable(WRITEPATH . 'logs') ? 'WRITABLE' : 'NOT WRITABLE';

        return $this->response
            ->setHeader('Content-Type', 'application/json')
            ->setBody(json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function compareData()
    {
        $gudang = \Config\Database::connect('gudang');
        $produksi = \Config\Database::connect('produksi');

        echo "<h2>Data Comparison: BECUM vs POWERJET1</h2>";

        // Get ALL BECUM records ordered by tanggal DESC  
        echo "<h3>BECUM Data (ALL records, newest first):</h3>";
        $becum = $gudang->table('mesin_becum')->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->limit(10)->get()->getResultArray();
        echo "<pre>";
        echo count($becum) . " total rows\n";
        foreach ($becum as $row) {
            echo "ID: {$row['id']}, SPK: {$row['no_spk']}, Mesin: {$row['nomor_mesin']}, Tanggal: {$row['tanggal']}\n";
        }
        echo "</pre>";

        // Get ALL POWERJET1 records
        echo "<h3>POWERJET1 Data (ALL records, newest first):</h3>";
        $powerjet = $gudang->table('mesin_powerjet1')->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->limit(10)->get()->getResultArray();
        echo "<pre>";
        echo count($powerjet) . " total rows\n";
        foreach ($powerjet as $row) {
            echo "ID: {$row['id']}, SPK: {$row['no_spk']}, Mesin: {$row['nomor_mesin']}, Tanggal: {$row['tanggal']}\n";
        }
        echo "</pre>";

        // Sample produksi data - GET ALL DATES
        echo "<h3>PRODUKSI Headers (sample, all dates):</h3>";
        $prod = $produksi->table('produksi_headers')
            ->select('tanggal', false)
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'DESC')
            ->get()->getResultArray();
        echo "<pre>";
        echo "Dates available in produksi_headers:\n";
        foreach ($prod as $row) {
            echo "  - {$row['tanggal']}\n";
        }
        echo "</pre>";

        // Get sample data for latest tanggal
        if (!empty($prod)) {
            $latestDate = $prod[0]['tanggal'];
            echo "<h3>PRODUKSI data for latest date ($latestDate):</h3>";
            $prodData = $produksi->table('produksi_headers')
                ->where('tanggal', $latestDate)
                ->limit(10)
                ->get()->getResultArray();
            echo "<pre>";
            foreach ($prodData as $row) {
                echo "SPK: {$row['no_spk']}, Mesin: {$row['nomor_mesin']}, Shift: {$row['shift']}, Bagus: {$row['grand_total_bagus']}\n";
            }
            echo "</pre>";
        }

        // Count rows in each mesin table
        echo "<h3>Row Counts:</h3>";
        echo "<pre>";
        $becumCount = $gudang->table('mesin_becum')->countAllResults();
        $powerjetCount = $gudang->table('mesin_powerjet1')->countAllResults();
        $prodCount = $produksi->table('produksi_headers')->countAllResults();
        echo "mesin_becum total rows: $becumCount\n";
        echo "mesin_powerjet1 total rows: $powerjetCount\n";
        echo "produksi_headers total rows: $prodCount\n";
        echo "</pre>";
    }
}
