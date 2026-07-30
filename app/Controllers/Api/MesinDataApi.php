<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

/**
 * API endpoint for fetching latest mesin data for PPIC menu display
 * Returns latest SPK, product, date per machine for real-time dashboard updates
 */
class MesinDataApi extends BaseController
{
    private const MACHINES = [
        'mesin_becum'     => 'Becum',
        'mesin_powerjet1' => 'Powerjet 1',
        'mesin_powerjet2' => 'Powerjet 2',
        'mesin_ccm1'      => 'CCM 1',
        'mesin_ccm2'      => 'CCM 2',
        'mesin_ips1'      => 'IPS 1',
        'mesin_ips2'      => 'IPS 2',
        'mesin_ips3'      => 'IPS 3',
        'mesin_ips4'      => 'IPS 4',
    ];

    private const TABLE_MAPPING = [
        'mesin_becum'     => 'mesin_becum',
        'mesin_powerjet1' => 'mesin_powerjet1',
        'mesin_powerjet2' => 'mesin_powerjet2',
        'mesin_ccm1'      => 'mesin_ccm1',
        'mesin_ccm2'      => 'mesin_ccm2',
        'mesin_ips1'      => 'mesin_ips1',
        'mesin_ips2'      => 'mesin_ips2',
        'mesin_ips3'      => 'mesin_ips3',
        'mesin_ips4'      => 'mesin_ips4',
    ];

    /**
     * GET /api/mesin-data/latest
     * Fetch latest data from all mesin tables
     * 
     * @return JSON array of mesin data with latest entries
     */
    public function latest()
    {
        try {
            $db = $this->dbGudang();
            $result = [];

            foreach (self::MACHINES as $slug => $label) {
                $table = self::TABLE_MAPPING[$slug] ?? null;
                if (!$table) continue;

                // Fetch latest entry from this mesin table, handling potential missing columns
                $query = $db->table($table)
                    ->where('deleted_at', null)
                    ->orderBy('created_at', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->limit(1)
                    ->get();

                $row = $query->getFirstRow('array');

                if ($row) {
                    // Build display data safely
                    $result[$slug] = [
                        'slug'          => $slug,
                        'label'         => $label,
                        'spk'           => $row['no_spk'] ?? '-',
                        'product'       => $this->getProductName($row, $slug),
                        'date'          => $row['tanggal'] ?? '-',
                        'time'          => $row['jam'] ?? '-',
                        'status'        => $row['status'] ?? 'unknown',
                        'nomor_mesin'   => $row['nomor_mesin'] ?? '-',
                        'grade'         => $row['grade'] ?? '-',
                        'last_updated'  => $row['created_at'] ?? date('Y-m-d H:i:s'),
                    ];
                } else {
                    // No data for this machine yet
                    $result[$slug] = [
                        'slug'          => $slug,
                        'label'         => $label,
                        'spk'           => '-',
                        'product'       => 'No data',
                        'date'          => '-',
                        'time'          => '-',
                        'status'        => 'idle',
                        'nomor_mesin'   => '-',
                        'grade'         => '-',
                        'last_updated'  => null,
                    ];
                }
            }

            return $this->response->setJSON([
                'status'  => 'success',
                'data'    => $result,
                'timestamp' => date('Y-m-d H:i:s'),
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'error'   => 'Failed to fetch mesin data',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * GET /api/mesin-data/:slug
     * Fetch data for a specific mesin
     */
    public function show($slug = null)
    {
        if (!$slug || !isset(self::TABLE_MAPPING[$slug])) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'error',
                'message' => 'Mesin tidak ditemukan'
            ]);
        }

        try {
            $db = $this->dbGudang();
            $table = self::TABLE_MAPPING[$slug];

            $rows = $db->table($table)
                ->where('deleted_at', null)
                ->orderBy('created_at', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(5)  // Get last 5 entries
                ->get()
                ->getResultArray();

            if (empty($rows)) {
                return $this->response->setJSON([
                    'status' => 'no_data',
                    'mesin'  => $slug,
                    'label'  => self::MACHINES[$slug] ?? $slug,
                    'data'   => [],
                ]);
            }

            $formatted = array_map(function($row) use ($slug) {
                return [
                    'id'            => $row['id'] ?? null,
                    'spk'           => $row['no_spk'] ?? '-',
                    'product'       => $this->getProductName($row, $slug),
                    'date'          => $row['tanggal'] ?? '-',
                    'time'          => $row['jam'] ?? '-',
                    'status'        => $row['status'] ?? 'unknown',
                    'nomor_mesin'   => $row['nomor_mesin'] ?? '-',
                    'grade'         => $row['grade'] ?? '-',
                    'brand'         => $row['brand'] ?? '-',
                    'created_at'    => $row['created_at'] ?? null,
                ];
            }, $rows);

            return $this->response->setJSON([
                'status' => 'success',
                'mesin'  => $slug,
                'label'  => self::MACHINES[$slug] ?? $slug,
                'data'   => $formatted,
            ]);

        } catch (\Throwable $e) {
            return $this->response->setStatusCode(500)->setJSON([
                'status'  => 'error',
                'error'   => 'Failed to fetch mesin data',
                'message' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Extract product name from mesin row based on mesin type
     */
    private function getProductName(array $row, string $slug): string
    {
        // Try common product field names
        if (!empty($row['produk_galon'])) return $row['produk_galon'];
        if (!empty($row['nama_produk'])) return $row['nama_produk'];
        if (!empty($row['product'])) return $row['product'];
        
        // Fallback
        return 'N/A';
    }
}
