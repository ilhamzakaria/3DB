<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddJamAwalAkhirToMesinBecum extends Migration
{
    protected $DBGroup = 'ppic';

    public function up()
    {
        // Add jam_awal and jam_akhir to all mesin tables in PPIC database
        $mesinTables = [
            'mesin_becum',
            'mesin_powerjet1',
            'mesin_powerjet2',
            'mesin_ccm1',
            'mesin_ccm2',
            'mesin_ips1',
            'mesin_ips2',
            'mesin_ips3',
            'mesin_ips4',
        ];

        foreach ($mesinTables as $table) {
            if (!$this->db->fieldExists('jam_awal', $table)) {
                $this->forge->addColumn($table, [
                    'jam_awal' => [
                        'type'       => 'TIME',
                        'null'       => true,
                        'comment'    => 'Jam awal aktivitas',
                    ],
                    'jam_akhir' => [
                        'type'       => 'TIME',
                        'null'       => true,
                        'comment'    => 'Jam akhir aktivitas',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        // Drop jam_awal and jam_akhir from all mesin tables
        $mesinTables = [
            'mesin_becum',
            'mesin_powerjet1',
            'mesin_powerjet2',
            'mesin_ccm1',
            'mesin_ccm2',
            'mesin_ips1',
            'mesin_ips2',
            'mesin_ips3',
            'mesin_ips4',
        ];

        foreach ($mesinTables as $table) {
            if ($this->db->fieldExists('jam_awal', $table)) {
                $this->forge->dropColumn($table, ['jam_awal', 'jam_akhir']);
            }
        }
    }
}
