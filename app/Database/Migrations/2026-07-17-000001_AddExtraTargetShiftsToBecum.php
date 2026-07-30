<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds extra_target_shifts (TEXT/JSON) column to mesin_becum.
 * Stores additional shifts beyond S1/S2/S3 as a JSON array:
 * [{"label": "Shift 4", "target": 500}, ...]
 *
 * Run twice:
 *   php spark migrate --group gudang
 *   php spark migrate --group ppic
 */
class AddExtraTargetShiftsToBecum extends Migration
{
    protected $DBGroup = 'gudang';

    public function up()
    {
        if (!$this->db->fieldExists('extra_target_shifts', 'mesin_becum')) {
            $this->forge->addColumn('mesin_becum', [
                'extra_target_shifts' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'after'   => 'target_shift3',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('extra_target_shifts', 'mesin_becum')) {
            $this->forge->dropColumn('mesin_becum', 'extra_target_shifts');
        }
    }
}
