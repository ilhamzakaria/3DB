<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTbOutRunnerToColorants extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_colorants', [
            'tb_out_runner' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'tb_in_runner',
                'comment' => 'TB out runner value — Powerjet machines',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_colorants', 'tb_out_runner');
    }
}
