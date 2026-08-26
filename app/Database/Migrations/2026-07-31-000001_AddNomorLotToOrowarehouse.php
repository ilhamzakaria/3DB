<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNomorLotToOrowarehouse extends Migration
{
    protected $DBGroup = 'warehouse';

    public function up()
    {
        if (!$this->db->fieldExists('nomor_lot', 'orowarehouse')) {
            $this->forge->addColumn('orowarehouse', [
                'nomor_lot' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'operator',
                ],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('orowarehouse', 'nomor_lot');
    }
}
