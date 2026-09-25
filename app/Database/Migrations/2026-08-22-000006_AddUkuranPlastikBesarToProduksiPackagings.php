<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUkuranPlastikBesarToProduksiPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_packagings', [
            'ukuran_plastik_besar' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'meter_pemakaian',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_packagings', 'ukuran_plastik_besar');
    }
}
