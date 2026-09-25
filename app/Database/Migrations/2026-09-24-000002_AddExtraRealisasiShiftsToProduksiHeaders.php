<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExtraRealisasiShiftsToProduksiHeaders extends Migration
{
    public function up()
    {
        // Add extra_realisasi_shifts column to produksi_headers table
        // Stores JSON array of extra shift realisasi data: [{"label":"Shift 4","realisasi":"100"}]
        
        if ($this->db->tableExists('produksi_headers')) {
            $fields = $this->db->getFieldData('produksi_headers');
            $columnExists = false;
            
            foreach ($fields as $field) {
                if ($field->name === 'extra_realisasi_shifts') {
                    $columnExists = true;
                    break;
                }
            }

            if (!$columnExists) {
                $this->forge->addColumn('produksi_headers', [
                    'extra_realisasi_shifts' => [
                        'type'       => 'LONGTEXT',
                        'null'       => true,
                        'default'    => null,
                        'after'      => 'grand_total_reject',
                        'comment'    => 'JSON array of extra shift realisasi data: [{"label":"Shift 4","realisasi":"100"}]',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('produksi_headers')) {
            $fields = $this->db->getFieldData('produksi_headers');
            foreach ($fields as $field) {
                if ($field->name === 'extra_realisasi_shifts') {
                    $this->forge->dropColumn('produksi_headers', 'extra_realisasi_shifts');
                    break;
                }
            }
        }
    }
}
