<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddKomposisiKgToProduksiMaterials extends Migration
{
    protected $DBGroup = 'produksi';

    public function up(): void
    {
        $db = \Config\Database::connect($this->DBGroup);
        
        // Check if columns already exist
        $columns = $db->getFieldData('produksi_materials');
        $columnNames = array_map(fn($col) => $col->name, $columns);
        
        // Only add columns that don't exist
        $fieldsToAdd = [];
        
        if (!in_array('komposisi_kg_1', $columnNames)) {
            $fieldsToAdd['komposisi_kg_1'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'default'    => null,
                'after'      => 'komposisi_persen_4',
            ];
        }
        
        if (!in_array('komposisi_kg_2', $columnNames)) {
            $fieldsToAdd['komposisi_kg_2'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'default'    => null,
                'after'      => 'komposisi_kg_1',
            ];
        }
        
        if (!in_array('komposisi_kg_3', $columnNames)) {
            $fieldsToAdd['komposisi_kg_3'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'default'    => null,
                'after'      => 'komposisi_kg_2',
            ];
        }
        
        if (!in_array('komposisi_kg_4', $columnNames)) {
            $fieldsToAdd['komposisi_kg_4'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,3',
                'null'       => true,
                'default'    => null,
                'after'      => 'komposisi_kg_3',
            ];
        }
        
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('produksi_materials', $fieldsToAdd);
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('produksi_materials', [
            'komposisi_kg_1',
            'komposisi_kg_2',
            'komposisi_kg_3',
            'komposisi_kg_4',
        ]);
    }
}
