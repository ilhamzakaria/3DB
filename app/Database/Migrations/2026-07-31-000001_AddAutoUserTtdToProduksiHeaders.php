<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAutoUserTtdToProduksiHeaders extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_headers', [
            'auto_user_ttd' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
                'after'      => 'ttd_spv',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_headers', 'auto_user_ttd');
    }
}
