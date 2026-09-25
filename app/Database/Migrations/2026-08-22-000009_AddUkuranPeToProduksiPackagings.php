<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUkuranPeToProduksiPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_packagings', [
            'ukuran_pe' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'plastik_kecil_pemakaian',
            ],
            'pe_awal' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'ukuran_pe',
            ],
            'pe_akhir' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'pe_awal',
            ],
            'pe_pemakaian' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'pe_akhir',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_packagings', [
            'ukuran_pe', 'pe_awal', 'pe_akhir', 'pe_pemakaian',
        ]);
    }
}
