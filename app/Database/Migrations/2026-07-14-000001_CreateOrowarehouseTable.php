<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrowarehouseTable extends Migration
{
    protected $DBGroup = 'warehouse';

    public function up()
    {
        // Table: orowarehouse (Barang Masuk dari Produksi - detailed tracking)
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'spk' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'shift' => [
                'type'       => 'ENUM',
                'constraint' => ['1', '2', '3'],
                'null'       => false,
            ],
            'grup' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'hasil_bagus' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => false,
                'default'    => 0,
            ],
            'hasil_reject' => [
                'type'       => 'DECIMAL',
                'constraint' => '12,2',
                'null'       => false,
                'default'    => 0,
            ],
            'material' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'colorant' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'packaging' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'operator' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['pending', 'diterima', 'ditolak'],
                'default'    => 'pending',
            ],
            'catatan' => [
                'type'  => 'TEXT',
                'null'  => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('spk');
        $this->forge->addKey('tanggal');
        $this->forge->addKey('status');
        $this->forge->createTable('orowarehouse');
    }

    public function down()
    {
        $this->forge->dropTable('orowarehouse');
    }
}
