<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJamShifToGudang extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('gudang')) {
            return;
        }

        $fields = [];

        if (!$this->db->fieldExists('jam', 'gudang')) {
            $fields['jam'] = [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => true,
                'after' => 'no_spk',
            ];
        }

        if (!$this->db->fieldExists('shif', 'gudang')) {
            $fields['shif'] = [
                'type' => 'VARCHAR',
                'constraint' => 10,
                'null' => true,
                'after' => 'jam',
            ];
        }

        if (!empty($fields)) {
            $this->forge->addColumn('gudang', $fields);
        }
    }

    public function down()
    {
        if (!$this->db->tableExists('gudang')) {
            return;
        }

        if ($this->db->fieldExists('shif', 'gudang')) {
            $this->forge->dropColumn('gudang', 'shif');
        }

        if ($this->db->fieldExists('jam', 'gudang')) {
            $this->forge->dropColumn('gudang', 'jam');
        }
    }
}
