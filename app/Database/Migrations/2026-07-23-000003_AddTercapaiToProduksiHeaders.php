<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTercapaiToProduksiHeaders extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_headers', [
            'tercapai' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'null' => false,
                'comment' => 'Status tercapai (target achieved): 0=No, 1=Yes',
                'after' => 'selesai_spk'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_headers', 'tercapai');
    }
}
