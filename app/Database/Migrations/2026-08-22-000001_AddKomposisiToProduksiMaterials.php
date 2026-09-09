<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add komposisi fields to produksi_materials table
 * 
 * Adds 4 pairs of nama and persentase fields for storing composition data
 * Run with: php spark migrate --group produksi
 */
class AddKomposisiToProduksiMaterials extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Add komposisi_nama fields (1-4)
        if (!$this->db->fieldExists('komposisi_nama_1', 'produksi_materials')) {
            $this->forge->addColumn('produksi_materials', [
                'komposisi_nama_1' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'lot_d',
                ],
                'komposisi_nama_2' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'komposisi_nama_3' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'komposisi_nama_4' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                ],
                'komposisi_persen_1' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                    'null'       => true,
                ],
                'komposisi_persen_2' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                    'null'       => true,
                ],
                'komposisi_persen_3' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                    'null'       => true,
                ],
                'komposisi_persen_4' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                    'null'       => true,
                ],
            ]);
        }
    }

    public function down()
    {
        $fieldsToRemove = [
            'komposisi_nama_1',
            'komposisi_nama_2',
            'komposisi_nama_3',
            'komposisi_nama_4',
            'komposisi_persen_1',
            'komposisi_persen_2',
            'komposisi_persen_3',
            'komposisi_persen_4',
        ];

        foreach ($fieldsToRemove as $field) {
            if ($this->db->fieldExists($field, 'produksi_materials')) {
                $this->forge->dropColumn('produksi_materials', $field);
            }
        }
    }
}
