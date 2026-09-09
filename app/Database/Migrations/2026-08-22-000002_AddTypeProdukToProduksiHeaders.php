<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTypeProdukToProduksiHeaders extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_headers', [
            'type_produk' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'batch_number',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_headers', 'type_produk');
    }
}
