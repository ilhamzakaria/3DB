<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataSeeder extends Seeder
{
    public function run()
    {
        $dbPpic = \Config\Database::connect('ppic');
        $dbGudang = \Config\Database::connect('gudang');
        $dbProduksi = \Config\Database::connect('produksi');

        $truncate = function ($db, $tables) {
            $db->query('SET FOREIGN_KEY_CHECKS = 0;');
            foreach ($tables as $table) {
                try {
                    if ($db->tableExists($table)) {
                        $db->table($table)->truncate();
                    }
                } catch (\Exception $e) { }
            }
            $db->query('SET FOREIGN_KEY_CHECKS = 1;');
        };

        $truncate($dbPpic, ['ppic']);
        $truncate($dbGudang, ['gudang', 'gudang_item_details']);
        $truncate($dbProduksi, [
            'produksi_headers', 
            'produksi_jam', 
            'produksi_rejects', 
            'produksi_materials', 
            'produksi_colorants', 
            'produksi_packaging', 
            'produksi_downtimes',
            'revisi_produksi'
        ]);

        $names = ['Budi', 'Andi', 'Siti', 'Dewi', 'Joko', 'Rina', 'Agus', 'Lani', 'Eko', 'Sari'];
        $mesins = ['IPS-01', 'IPS-02', 'CCM-01', 'CCM-02', 'BECUM-01'];
        $produks = ['Preform 19gr', 'Preform 27gr', 'Cap 30/25', 'Galon 19L', 'Tutup Galon'];
        $grades = ['A', 'B', 'C'];
        $colors = ['Biru', 'Hijau', 'Merah', 'Putih', 'Kuning'];

        // Seed PPIC
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'id_user'     => 1,
                'no_spk'      => 'SPK-' . rand(1000, 9999),
                'jam'         => '07-08',
                'nama_mesin'  => $mesins[array_rand($mesins)],
                'nama_produk' => $produks[array_rand($produks)],
                'grade'       => $grades[array_rand($grades)],
                'warna'       => $colors[array_rand($colors)],
                'nomor_mesin' => (string) rand(1, 10),
                'shif'        => (string) rand(1, 3),
                'operator'    => $names[array_rand($names)],
                'targett'     => (string) rand(1000, 5000),
                'tanggal'     => date('Y-m-d', strtotime('-' . rand(0, 30) . ' days')),
            ];
            $dbPpic->table('ppic')->insert($data);
        }

        // Seed Plant Produksi
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'nomor_spk'     => 'SPK-' . rand(1000, 9999),
                'nama_mesin'    => $mesins[array_rand($mesins)],
                'nama_produksi' => $produks[array_rand($produks)],
                'batch_number'  => 'BATCH-' . rand(100, 999),
                'shift'         => (string) rand(1, 3),
                'grup'          => chr(rand(65, 68)),
                'nomor_mesin'   => (string) rand(1, 5),
                'packing'       => rand(0, 1) ? 'Box' : 'Karung',
                'cycle_time'    => rand(500, 2000) / 100,
                'target'        => rand(5000, 20000),
                'isi'           => rand(50, 100),
                'tanggal'       => date('Y-m-d', strtotime('-' . rand(0, 30) . ' days')),
                'operator'      => $names[array_rand($names)],
                'grand_total_bagus' => rand(4000, 19000),
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ];
            $dbProduksi->table('produksi_headers')->insert($data);
        }

        // Seed Gudang
        $gudangCols = $dbGudang->getFieldNames('gudang');
        for ($i = 0; $i < 100; $i++) {
            $data = [
                'id_user'      => 1,
                'no_spk'       => 'SPK-' . rand(1000, 9999),
                'shif'         => (string) rand(1, 3),
                'bahan_baku'   => (string) rand(10, 100),
                'box'          => (string) rand(50, 200),
                'karung'       => (string) rand(20, 100),
                'plastik'      => (string) rand(100, 500),
                'masterbatch'  => (string) rand(5, 50),
                'tanggal'      => date('Y-m-d', strtotime('-' . rand(0, 30) . ' days')),
            ];
            if (in_array('jam', $gudangCols)) {
                $data['jam'] = '08:00';
            }
            $dbGudang->table('gudang')->insert($data);
        }
    }
}
