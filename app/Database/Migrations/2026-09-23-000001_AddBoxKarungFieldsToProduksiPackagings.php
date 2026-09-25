<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBoxKarungFieldsToProduksiPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $fields = [
            'ukuran_box_karung' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'pe_pemakaian',
            ],
            'box_karung_awal' => [
                'type'    => 'DECIMAL',
                'constraint' => '10,2',
                'null'    => true,
                'default' => null,
                'after'   => 'ukuran_box_karung',
            ],
            'box_karung_akhir' => [
                'type'    => 'DECIMAL',
                'constraint' => '10,2',
                'null'    => true,
                'default' => null,
                'after'   => 'box_karung_awal',
            ],
            'box_karung_pemakaian' => [
                'type'    => 'DECIMAL',
                'constraint' => '10,2',
                'null'    => true,
                'default' => null,
                'after'   => 'box_karung_akhir',
            ],
        ];

        $this->forge->addColumn('produksi_packagings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_packagings', [
            'ukuran_box_karung',
            'box_karung_awal',
            'box_karung_akhir',
            'box_karung_pemakaian',
        ]);
    }
}
