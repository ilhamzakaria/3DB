<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLotStatusToMesinBecum extends Migration
{
    protected $DBGroup = 'gudang';

    public function up()
    {
        if (!$this->db->tableExists('mesin_becum')) {
            return;
        }

        $existing = [];
        foreach ($this->db->getFieldNames('mesin_becum') as $name) {
            $existing[strtolower($name)] = true;
        }

        $fields = [];
        if (!isset($existing['nomor_lot'])) {
            $fields['nomor_lot'] = ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true, 'after' => 'stock'];
        }
        if (!isset($existing['status'])) {
            $fields['status'] = ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true, 'default' => 'menggiling', 'after' => 'nomor_lot'];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('mesin_becum', $fields);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('mesin_becum', ['nomor_lot', 'status']);
    }
}