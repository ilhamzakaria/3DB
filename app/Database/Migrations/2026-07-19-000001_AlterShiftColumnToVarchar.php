<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterShiftColumnToVarchar extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Ubah kolom shift dari INT(1) ke VARCHAR(50)
        // agar bisa menyimpan: "1", "2", "3", "extra_1", "extra_2", dst.
        $this->forge->modifyColumn('produksi_headers', [
            'shift' => [
                'name'       => 'shift',
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('produksi_headers', [
            'shift' => [
                'name'       => 'shift',
                'type'       => 'INT',
                'constraint' => 1,
                'null'       => true,
            ],
        ]);
    }
}
