<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRealisasiAwalAkhirToPpic extends Migration
{
    protected $DBGroup = 'ppic';

    public function up()
    {
        $fields = [
            'realisasi_awal' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'after'   => 'target_akhir',
            ],
            'realisasi_akhir' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'after'   => 'realisasi_awal',
            ],
        ];

        $existing = [];
        if ($this->db->tableExists('ppic')) {
            foreach ($this->db->getFieldNames('ppic') as $name) {
                $existing[strtolower($name)] = true;
            }
        }

        $toAdd = [];
        foreach ($fields as $name => $def) {
            if (!isset($existing[strtolower($name)])) {
                $toAdd[$name] = $def;
            }
        }

        if (!empty($toAdd)) {
            $this->forge->addColumn('ppic', $toAdd);
        }
    }

    public function down()
    {
        $this->forge->dropColumn('ppic', ['realisasi_awal', 'realisasi_akhir']);
    }
}
