<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipeToMasterbatch extends Migration
{
    protected $DBGroup = 'gudang';

    public function up()
    {
        if ($this->db->fieldExists('tipe', 'masterbatch')) {
            return;
        }

        $fields = [
            'tipe' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'produk',
            ],
        ];

        $this->forge->addColumn('masterbatch', $fields);
    }

    public function down()
    {
        if ($this->db->fieldExists('tipe', 'masterbatch')) {
            $this->forge->dropColumn('masterbatch', 'tipe');
        }
    }
}
