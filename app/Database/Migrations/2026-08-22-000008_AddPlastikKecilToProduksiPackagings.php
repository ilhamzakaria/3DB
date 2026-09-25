<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlastikKecilToProduksiPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_packagings', [
            'ukuran_plastik_kecil' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'plastik_besar_pemakaian',
            ],
            'plastik_kecil_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'ukuran_plastik_kecil',
            ],
            'plastik_kecil_akhir' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'plastik_kecil_awal',
            ],
            'plastik_kecil_pemakaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'plastik_kecil_akhir',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_packagings', [
            'ukuran_plastik_kecil',
            'plastik_kecil_awal',
            'plastik_kecil_akhir',
            'plastik_kecil_pemakaian',
        ]);
    }
}
