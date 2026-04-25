<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBahanBakuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'nama_bahan' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['menggiling', 'bahan_masuk', 'bahan_keluar'],
                'default'    => 'menggiling',
            ],
            'kode' => [
                'type'       => 'VARCHAR',
                'constraint' => '55',
                'null'       => false,
            ],
            'jumlah' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'nomor_lot' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'no_spk' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('bahan_baku');
    }

    public function down()
    {
        $this->forge->dropTable('bahan_baku');
    }
}
