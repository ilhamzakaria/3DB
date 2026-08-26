<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterPlastikBagusRejectToDecimal extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $fields = [
            'plastik_bagus' => [
                'name'       => 'plastik_bagus',
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
            ],
            'plastik_reject' => [
                'name'       => 'plastik_reject',
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
            ],
        ];

        $this->forge->modifyColumn('produksi_packagings', $fields);
    }

    public function down()
    {
        $fields = [
            'plastik_bagus' => [
                'name'       => 'plastik_bagus',
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'plastik_reject' => [
                'name'       => 'plastik_reject',
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
        ];

        $this->forge->modifyColumn('produksi_packagings', $fields);
    }
}
