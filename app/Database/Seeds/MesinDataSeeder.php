<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MesinDataSeeder extends Seeder
{
    protected $DBGroup = 'gudang';

    public function run()
    {
        // Base data arrays
        $galon_names = ['Galon A', 'Galon B', 'Galon C', 'Galon D', 'Galon E'];
        $cap_names = ['Cap Galon A', 'Cap Galon B', 'Cap Galon C'];
        $preform_names = ['Preform A', 'Preform B', 'Preform C', 'Preform D'];
        $grades = ['A+', 'A', 'B', 'B.1.V-3', 'C', 'D', 'D.0.V-2'];
        $brands = ['Brand Alpha', 'Brand Beta', 'Brand Gamma', 'Brand Delta'];
        $colors = ['Bening', 'Biru', 'Hijau', 'Merah', 'Putih'];
        $keterangan_list = ['Standar', 'Premium', 'Test', 'Sample'];

        // Define table-specific configurations
        $configs = [
            'mesin_becum' => [
                'product_field' => 'produk_galon',
                'product_values' => $galon_names,
                'has_berat' => true,
            ],
            'mesin_powerjet1' => [
                'product_field' => 'produk_cap_galon',
                'product_values' => $cap_names,
                'has_berat' => false,
            ],
            'mesin_powerjet2' => [
                'product_field' => 'produk_cap_galon',
                'product_values' => $cap_names,
                'has_berat' => false,
            ],
            'mesin_ccm1' => [
                'product_field' => 'produk_cap_galon',
                'product_values' => $cap_names,
                'has_berat' => false,
            ],
            'mesin_ccm2' => [
                'product_field' => 'produk_cap_galon',
                'product_values' => $cap_names,
                'has_berat' => false,
            ],
            'mesin_ips1' => [
                'product_field' => 'produk_preform',
                'product_values' => $preform_names,
                'has_berat' => false,
            ],
            'mesin_ips2' => [
                'product_field' => 'produk_preform',
                'product_values' => $preform_names,
                'has_berat' => false,
            ],
            'mesin_ips3' => [
                'product_field' => 'produk_preform',
                'product_values' => $preform_names,
                'has_berat' => false,
            ],
            'mesin_ips4' => [
                'product_field' => 'produk_preform',
                'product_values' => $preform_names,
                'has_berat' => false,
            ],
        ];

        foreach ($configs as $table => $config) {
            for ($i = 1; $i <= 10; $i++) {
                $baseDate = strtotime('-' . rand(0, 30) . ' days');
                $baseTime = date('Y-m-d H:i:s', $baseDate);
                $endTime = date('Y-m-d H:i:s', strtotime('+' . rand(1, 4) . ' hours', $baseDate));

                // Build row with common fields
                $row = [
                    'tanggal'               => date('Y-m-d', $baseDate),
                    'no_spk'                => rand(1000, 9999),
                    'nomor_mesin'           => 'M-' . str_pad(rand(1, 50), 3, '0', STR_PAD_LEFT),
                    'shif'                  => (string)rand(1, 3),
                    'brand'                 => $brands[array_rand($brands)],
                    'grade'                 => $grades[array_rand($grades)],
                    // Target & Realisasi
                    'jam'                   => $baseTime,
                    'waktu_akhir_target'    => $endTime,
                    'target_shift1'         => rand(400, 600),
                    'target_shift2'         => rand(350, 550),
                    'target_shift3'         => rand(300, 500),
                    'waktu_awal_realisasi'  => $baseTime,
                    'waktu_akhir_realisasi' => $endTime,
                    'realisasi_shift1'      => rand(350, 580),
                    'realisasi_shift2'      => rand(300, 530),
                    'realisasi_shift3'      => rand(250, 480),
                    'total_spk'             => rand(1000, 2000),
                    'packing'               => rand(1, 10) . '-' . rand(1, 10),
                    'keterangan'            => $keterangan_list[array_rand($keterangan_list)] . ' ' . rand(1, 99),
                    'revisi'                => 'Komposisi ' . rand(1, 100) . '%',
                    'created_at'            => $baseTime,
                    'updated_at'            => $baseTime,
                ];

                // Add product-specific field
                $row[$config['product_field']] = $config['product_values'][array_rand($config['product_values'])];

                // Add berat if table supports it
                if ($config['has_berat']) {
                    $row['berat'] = rand(90, 120);
                }

                // Add warna for certain tables
                if (in_array($config['product_field'], ['produk_cap_galon', 'produk_preform'])) {
                    $row['warna'] = $colors[array_rand($colors)];
                }

                try {
                    $this->db->table($table)->insert($row);
                } catch (\Exception $e) {
                    echo "Error inserting into $table (row $i): " . $e->getMessage() . "\n";
                }
            }
            echo "✓ Inserted 10 random records into $table\n";
        }
    }
}
