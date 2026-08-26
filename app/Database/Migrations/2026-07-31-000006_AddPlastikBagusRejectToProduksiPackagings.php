<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlastikBagusRejectToProduksiPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $fields = [
            'plastik_bagus' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'plastik',
            ],
            'plastik_reject' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'plastik_bagus',
            ],
        ];

        $this->forge->addColumn('produksi_packagings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_packagings', ['plastik_bagus', 'plastik_reject']);
    }
}
