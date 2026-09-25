<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTbLainInToColorants extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $fields = [
            'tb_lain_in' => [
                'type'       => 'TEXT',
                'null'       => true,
                'default'    => null,
                'after'      => 'tb_out_details',
            ],
            'tb_lain_in_details' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'tb_lain_in',
            ],
        ];

        $this->forge->addColumn('produksi_colorants', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_colorants', ['tb_lain_in', 'tb_lain_in_details']);
    }
}
