<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Same as AddExtraTargetShiftsToBecum but targets the ppic database group.
 * Run with: php spark migrate --group ppic
 */
class AddExtraTargetShiftsToBecumPpic extends Migration
{
    protected $DBGroup = 'ppic';

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
