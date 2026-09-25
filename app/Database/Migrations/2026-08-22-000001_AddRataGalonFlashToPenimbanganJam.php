<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRataGalonFlashToPenimbanganJam extends Migration
{
    protected $DBGroup = 'master';

    public function up()
    {
        $this->forge->addColumn('penimbangan_jam', [
            'rata_galon' => [
                'type'       => 'DECIMAL',
                'constraint' => [8, 2],
                'default'    => 0,
                'null'       => true,
                'comment'    => 'Rata-rata galon (total galon / 8)',
                'after'      => 'flash',
            ],
            'rata_flash' => [
                'type'       => 'DECIMAL',
                'constraint' => [8, 2],
                'default'    => 0,
                'null'       => true,
                'comment'    => 'Rata-rata flash (total flash / 8)',
                'after'      => 'rata_galon',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('penimbangan_jam', ['rata_galon', 'rata_flash']);
    }
}
