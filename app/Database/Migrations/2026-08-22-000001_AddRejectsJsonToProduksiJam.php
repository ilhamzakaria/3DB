<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRejectsJsonToProduksiJam extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_jam', [
            'rejects_json' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'total_reject',
                'comment' => 'JSON array of reject details per jam: [{"jenis":"START UP","jumlah":5}]',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_jam', 'rejects_json');
    }
}
