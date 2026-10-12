<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNamaMesinToBarangMasukProduksi extends Migration
{
    public function up()
    {
        // Check if table exists before altering
        if ($this->db->tableExists('barang_masuk_produksi', false)) {
            $this->forge->addColumn('barang_masuk_produksi', [
                'nama_mesin' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => false,
                    'default'    => '',
                    'after'      => 'tanggal',
                    'comment'    => 'Nama mesin (Becum, Powerjet 1-2, CCM 1-2, IPS 1-4)'
                ],
            ]);

            // Add index untuk faster queries
            $this->db->query('ALTER TABLE barang_masuk_produksi ADD INDEX idx_bmp_nama_mesin (nama_mesin)');
        } else {
            // Table doesn't exist, create it
            $this->forge->addField([
                'id'              => ['type' => 'INT', 'auto_increment' => true],
                'nomor_spk'       => ['type' => 'VARCHAR', 'constraint' => 100],
                'nomor_lot'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tanggal'         => ['type' => 'DATETIME'],
                'nama_mesin'      => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => ''],
                'no_spk'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'nama_produksi'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'kode_barang'     => ['type' => 'VARCHAR', 'constraint' => 100],
                'nama_barang_1'   => ['type' => 'VARCHAR', 'constraint' => 255],
                'nama_barang_2'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'nama_barang_3'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'nama_barang_4'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'satuan'          => ['type' => 'VARCHAR', 'constraint' => 50],
                'jumlah_keluar'   => ['type' => 'INT'],
                'jumlah_terima'   => ['type' => 'INT', 'null' => true],
                'nama_penerima'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'catatan'         => ['type' => 'TEXT', 'null' => true],
                'status'          => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'pending'],
                'created_at'      => ['type' => 'DATETIME', 'null' => true],
                'updated_at'      => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'      => ['type' => 'DATETIME', 'null' => true],
            ]);
            
            $this->forge->addKey('id', true);
            $this->forge->addKey('nama_mesin', false, false, 'idx_bmp_nama_mesin');
            $this->forge->createTable('barang_masuk_produksi');
        }
    }

    public function down()
    {
        if ($this->db->tableExists('barang_masuk_produksi', false)) {
            // Only drop index if table exists
            try {
                $this->db->query('ALTER TABLE barang_masuk_produksi DROP INDEX idx_bmp_nama_mesin');
            } catch (\Exception $e) {
                // Index might not exist, ignore
            }
            
            // Check if nama_mesin column exists before dropping
            if ($this->forge->fieldExists('nama_mesin', 'barang_masuk_produksi')) {
                $this->forge->dropColumn('barang_masuk_produksi', 'nama_mesin');
            }
        }
    }
}

