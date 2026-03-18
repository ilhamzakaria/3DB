<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProduktions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 5,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'no_spk' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'tanggal' => [
                'type' => 'DATE',
            ],
            'nama_mesin' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'nama_produk' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'batch_number' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'shift' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'nomor_mesin' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'packing_isi' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'cycle_time' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'target' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'operator' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
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
        $this->forge->createTable('produktions');
    }

    public function down()
    {
        $this->forge->dropTable('produktions');
    }
}
