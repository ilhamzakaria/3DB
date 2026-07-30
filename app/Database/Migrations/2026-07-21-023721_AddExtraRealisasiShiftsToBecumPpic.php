<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add extra_realisasi_shifts column to mesin_becum table in PPIC database.
 * This column stores JSON data for extra shift realisasi values.
 * Run with: php spark migrate --group ppic
 */
class AddExtraRealisasiShiftsToBecumPpic extends Migration
{
    protected $DBGroup = 'ppic';

    public function up()
    {
        if (!$this->db->fieldExists('extra_realisasi_shifts', 'mesin_becum')) {
            $this->forge->addColumn('mesin_becum', [
                'extra_realisasi_shifts' => [
                    'type'    => 'TEXT',
                    'null'    => true,
                    'default' => null,
                    'comment' => 'JSON array of extra shift realisasi values',
                    'after'   => 'realisasi_shift3',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('extra_realisasi_shifts', 'mesin_becum')) {
            $this->forge->dropColumn('mesin_becum', 'extra_realisasi_shifts');
        }
    }
}
