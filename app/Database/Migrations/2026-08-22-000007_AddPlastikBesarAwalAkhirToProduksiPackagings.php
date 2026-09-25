<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlastikBesarAwalAkhirToProduksiPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_packagings', [
            'plastik_besar_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'ukuran_plastik_besar',
            ],
            'plastik_besar_akhir' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'plastik_besar_awal',
            ],
            'plastik_besar_pemakaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'plastik_besar_akhir',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_packagings', [
            'plastik_besar_awal',
            'plastik_besar_akhir',
            'plastik_besar_pemakaian',
        ]);
    }
}
