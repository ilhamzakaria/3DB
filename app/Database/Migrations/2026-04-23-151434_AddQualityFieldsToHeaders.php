<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddQualityFieldsToHeaders extends Migration
{
    public function up()
    {
        $this->forge->addColumn('produksi_headers', [
            'sisa_po'  => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'hold'     => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'gumpalan' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_headers', ['sisa_po', 'hold', 'gumpalan']);
    }
}
