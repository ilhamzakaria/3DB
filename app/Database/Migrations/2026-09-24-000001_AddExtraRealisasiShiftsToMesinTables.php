<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExtraRealisasiShiftsToMesinTables extends Migration
{
    public function up()
    {
        // Add extra_realisasi_shifts column to 8 mesin tables (Powerjet, CCM, IPS)
        // Becum already has this column from migration 2026-07-21-023605
        
        $tables = [
            'mesin_powerjet1',
            'mesin_powerjet2',
            'mesin_ccm1',
            'mesin_ccm2',
            'mesin_ips1',
            'mesin_ips2',
            'mesin_ips3',
            'mesin_ips4',
        ];

        foreach ($tables as $table) {
            if ($this->db->tableExists($table)) {
                // Check if column already exists to avoid duplication
                $fields = $this->db->getFieldData($table);
                $columnExists = false;
                
                foreach ($fields as $field) {
                    if ($field->name === 'extra_realisasi_shifts') {
                        $columnExists = true;
                        break;
                    }
                }

                if (!$columnExists) {
                    $this->forge->addColumn($table, [
                        'extra_realisasi_shifts' => [
                            'type'       => 'LONGTEXT',
                            'null'       => true,
                            'default'    => null,
                            'after'      => 'target_shift3',  // Position after target_shift3
                            'comment'    => 'JSON array of extra shift realisasi data {[{label, realisasi}]}',
                        ],
                    ]);
                }
            }
        }
    }

    public function down()
    {
        $tables = [
            'mesin_powerjet1',
            'mesin_powerjet2',
            'mesin_ccm1',
            'mesin_ccm2',
            'mesin_ips1',
            'mesin_ips2',
            'mesin_ips3',
            'mesin_ips4',
        ];

        foreach ($tables as $table) {
            if ($this->db->tableExists($table)) {
                $fields = $this->db->getFieldData($table);
                foreach ($fields as $field) {
                    if ($field->name === 'extra_realisasi_shifts') {
                        $this->forge->dropColumn($table, 'extra_realisasi_shifts');
                        break;
                    }
                }
            }
        }
    }
}
