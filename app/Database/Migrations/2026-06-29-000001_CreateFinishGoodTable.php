<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinishGoodTable extends Migration
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
            'kode_barang' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'kode_warna' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'item' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'stok_maximum' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'stok_minimum' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'unit' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'default'    => 'pcs',
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'awal' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'sumber_in' => [
                'type'       => 'ENUM',
                'constraint' => ['suplier', 'produksi'],
                'default'    => 'suplier',
            ],
            'out' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'akhir' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
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
        $this->forge->addKey('tanggal');
        $this->forge->createTable('finish_good');
    }

    public function down()
    {
        $this->forge->dropTable('finish_good');
    }
}
