<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNomorMesinToOrowarehouse extends Migration
{
    protected $DBGroup = 'warehouse';

    public function up()
    {
        $fields = [
            'nomor_mesin' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'nomor_lot',
            ],
        ];

        $this->forge->addColumn('orowarehouse', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('orowarehouse', ['nomor_mesin']);
    }
}
