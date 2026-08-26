<?php

namespace App\Controllers;

/**
 * Database Connection Tester
 * Access via: /test-db
 * 
 * PENTING: Hapus file ini setelah selesai debugging!
 */
class TestDb extends BaseController
{
    public function index()
    {
        // Only allow in development
        if (ENVIRONMENT !== 'development' && !in_array(session('role'), ['admin'])) {
            return 'Access denied. Only available in development or admin.';
        }

        $results = [];
        
        // Test Produksi DB
        try {
            $db = \Config\Database::connect('produksi');
            $results['produksi'] = [
                'status'   => 'Connected',
                'group'    => 'produksi',
                'database' => $db->getDatabase(),
                'version'  => $db->getVersion(),
                'hostname' => $db->hostname ?? 'hidden',
            ];

            // Test insert ke produksi_headers
            $testData = [
                'nomor_spk'  => 'TEST-' . time(),
                'nama_mesin' => 'TEST',
                'tanggal'    => date('Y-m-d'),
                'created_at' => date('Y-m-d H:i:s'),
            ];
            $db->table('produksi_headers')->insert($testData);
            $insertId = $db->insertID();

            if ($insertId) {
                $results['produksi']['test_insert'] = 'OK - ID: ' . $insertId;
                $db->table('produksi_headers')->where('id', $insertId)->delete();
            } else {
                $results['produksi']['test_insert'] = 'FAILED: ' . json_encode($db->error());
            }
        } catch (\Exception $e) {
            $results['produksi'] = ['status' => 'Error', 'message' => $e->getMessage()];
        }

        // Test PPIC DB
        try {
            $db = \Config\Database::connect('ppic');
            $results['ppic'] = [
                'status'   => 'Connected',
                'group'    => 'ppic',
                'database' => $db->getDatabase(),
            ];
        } catch (\Exception $e) {
            $results['ppic'] = ['status' => 'Error', 'message' => $e->getMessage()];
        }

        // Test Gudang DB
        try {
            $db = \Config\Database::connect('gudang');
            $results['gudang'] = [
                'status'   => 'Connected',
                'group'    => 'gudang',
                'database' => $db->getDatabase(),
            ];
        } catch (\Exception $e) {
            $results['gudang'] = ['status' => 'Error', 'message' => $e->getMessage()];
        }

        // Test Master DB
        try {
            $db = \Config\Database::connect('master');
            $results['master'] = [
                'status'   => 'Connected',
                'group'    => 'master',
                'database' => $db->getDatabase(),
            ];
        } catch (\Exception $e) {
            $results['master'] = ['status' => 'Error', 'message' => $e->getMessage()];
        }

        // Show .env path info
        $results['env_info'] = [
            'CI_ENVIRONMENT' => ENVIRONMENT,
            'APPPATH'        => APPPATH,
            'ROOTPATH'       => ROOTPATH,
            'env_file_exists' => file_exists(ROOTPATH . '.env') ? 'YES' : 'NO — THIS IS THE PROBLEM!',
        ];

        // Output results
        header('Content-Type: application/json');
        echo json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }
}
