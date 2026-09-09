<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlashKotoSapuanToProduksiHeaders extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_headers', [
            'plash_kotor' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'gumpalan',
                'comment'    => 'Plash Kotor - Becum machine only'
            ],
            'sapuan' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'plash_kotor',
                'comment'    => 'Sapuan - Becum machine only'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_headers', ['plash_kotor', 'sapuan']);
    }
}
