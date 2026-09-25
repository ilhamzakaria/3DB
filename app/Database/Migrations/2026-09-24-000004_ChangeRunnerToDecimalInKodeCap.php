<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeRunnerToDecimalInKodeCap extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Change runner column from TINYINT(2) to DECIMAL(8,2) to support decimal values
        $this->forge->modifyColumn('produksi_kode_cap', [
            'runner' => [
                'type'       => 'DECIMAL',
                'constraint' => [8, 2],
                'default'    => 0,
                'comment'    => 'Runner value - supports decimal (e.g., 12.50)',
            ],
        ]);
    }

    public function down()
    {
        // Revert to TINYINT(2) if needed
        $this->forge->modifyColumn('produksi_kode_cap', [
            'runner' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'default'    => 0,
            ],
        ]);
    }
}
