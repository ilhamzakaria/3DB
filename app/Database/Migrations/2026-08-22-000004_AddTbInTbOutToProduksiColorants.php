<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTbInTbOutToProduksiColorants extends Migration
{
    protected $DBGroup = 'produksi';

    public function up(): void
    {
        $db = \Config\Database::connect($this->DBGroup);
        
        // Check if columns already exist
        $columns = $db->getFieldData('produksi_colorants');
        $columnNames = array_map(fn($col) => $col->name, $columns);
        
        // Only add columns that don't exist
        $fieldsToAdd = [];
        
        if (!in_array('tb_in', $columnNames)) {
            $fieldsToAdd['tb_in'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'pemakaian',
            ];
        }
        
        if (!in_array('tb_out', $columnNames)) {
            $fieldsToAdd['tb_out'] = [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'tb_in',
            ];
        }
        
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('produksi_colorants', $fieldsToAdd);
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('produksi_colorants', ['tb_in', 'tb_out']);
    }
}
