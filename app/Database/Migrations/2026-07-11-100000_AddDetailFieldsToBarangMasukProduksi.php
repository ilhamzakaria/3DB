<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailFieldsToBarangMasukProduksi extends Migration
{
    public function up()
    {
        // Tambah kolom detail produksi ke tabel barang_masuk_produksi
        $fields = [
            'spk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'no_dokumen',
            ],
            'produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'spk',
            ],
            'shift' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '2', '3'],
                'null'       => true,
                'after'      => 'produk',
            ],
            'grup' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'shift',
            ],
            'hasil_bagus' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'grup',
            ],
            'hasil_reject' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => true,
                'default'    => 0,
                'after'      => 'hasil_bagus',
            ],
            'material' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'hasil_reject',
            ],
            'colorant' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'material',
            ],
            'packaging' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'colorant',
            ],
            'operator' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'packaging',
            ],
        ];

        $this->forge->addColumn('barang_masuk_produksi', $fields);

        // Tambah index untuk performa query
        $this->db->query('ALTER TABLE `barang_masuk_produksi` ADD INDEX `idx_bmp_spk` (`spk`)');
        $this->db->query('ALTER TABLE `barang_masuk_produksi` ADD INDEX `idx_bmp_shift` (`shift`)');
        $this->db->query('ALTER TABLE `barang_masuk_produksi` ADD INDEX `idx_bmp_status` (`status`)');
    }

    public function down()
    {
        $this->forge->dropColumn('barang_masuk_produksi', [
            'spk', 'produk', 'shift', 'grup',
            'hasil_bagus', 'hasil_reject',
            'material', 'colorant', 'packaging', 'operator',
        ]);
    }
}
