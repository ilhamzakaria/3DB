<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTbDetailsToColorants extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addColumn('produksi_colorants', [
            'tb_in_details' => [
                'type'    => 'TEXT',
                'null'    => true,
                'after'   => 'tb_in',
                'comment' => 'JSON array of TB in items: [{nama, persen, lot}] — Becum only',
            ],
            'tb_out_details' => [
                'type'    => 'TEXT',
                'null'    => true,
                'after'   => 'tb_out',
                'comment' => 'JSON array of TB out items: [{nama, persen, lot}] — Becum only',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_colorants', ['tb_in_details', 'tb_out_details']);
    }
}
