<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGudangModulesBatch1 extends Migration
{
    public function up()
    {
        // 1. Gilingan Galon
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'nama_gilingan' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => false],
            'sumber_gilingan' => ['type' => 'ENUM', 'constraint' => ['supplier', 'produksi'], 'default' => 'produksi'],
            'status' => ['type' => 'ENUM', 'constraint' => ['menggiling', 'bahan_masuk', 'bahan_keluar'], 'default' => 'bahan_masuk'],
            'kode' => ['type' => 'VARCHAR', 'constraint' => '55', 'null' => true],
            'jumlah' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'nomor_lot' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'no_spk' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'shif' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'tanggal' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('gilingan_galon');

        // 2. Stiker
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'nama_stiker' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => false],
            'status' => ['type' => 'ENUM', 'constraint' => ['menggiling', 'bahan_masuk', 'bahan_keluar'], 'default' => 'bahan_masuk'],
            'kode' => ['type' => 'VARCHAR', 'constraint' => '55', 'null' => true],
            'jumlah' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'nomor_lot' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'no_spk' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'shif' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'tanggal' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('stiker_gudang');

        // 3. Reject Produksi / Limbah
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'nama_limbah' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => false],
            'jenis_limbah' => ['type' => 'ENUM', 'constraint' => ['reject_preform', 'bekuan_pet', 'bekuan_cap_galon', 'gilingan_screwcap'], 'default' => 'reject_preform'],
            'status' => ['type' => 'ENUM', 'constraint' => ['menggiling', 'bahan_masuk', 'bahan_keluar'], 'default' => 'bahan_masuk'],
            'kode' => ['type' => 'VARCHAR', 'constraint' => '55', 'null' => true],
            'jumlah' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'nomor_lot' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'no_spk' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'shif' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'tanggal' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('reject_produksi');
    }

    public function down()
    {
        $this->forge->dropTable('gilingan_galon');
        $this->forge->dropTable('stiker_gudang');
        $this->forge->dropTable('reject_produksi');
    }
}
