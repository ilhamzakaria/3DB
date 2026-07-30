<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBamTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'tipe' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => false],
            'nama_bam' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => false],
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
        $this->forge->createTable('bam', true);
    }

    public function down()
    {
        $this->forge->dropTable('bam', true);
    }
}
