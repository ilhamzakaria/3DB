<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTbInRunnerToColorants extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_colorants', [
            'tb_in_runner' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'tb_lain_in_details',
                'comment' => 'TB in runner value — Powerjet machines',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_colorants', 'tb_in_runner');
    }
}
