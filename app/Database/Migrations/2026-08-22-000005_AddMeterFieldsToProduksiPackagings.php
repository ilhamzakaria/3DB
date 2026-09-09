<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMeterFieldsToProduksiPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up(): void
    {
        $db = \Config\Database::connect($this->DBGroup);
        
        // Check if columns already exist
        $columns = $db->getFieldData('produksi_packagings');
        $columnNames = array_map(fn($col) => $col->name, $columns);
        
        // Only add columns that don't exist
        $fieldsToAdd = [];
        
        if (!in_array('meter_awal', $columnNames)) {
            $fieldsToAdd['meter_awal'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'nicktainer_ukuran',
            ];
        }
        
        if (!in_array('meter_akhir', $columnNames)) {
            $fieldsToAdd['meter_akhir'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'meter_awal',
            ];
        }
        
        if (!in_array('meter_pemakaian', $columnNames)) {
            $fieldsToAdd['meter_pemakaian'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'after'      => 'meter_akhir',
            ];
        }
        
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('produksi_packagings', $fieldsToAdd);
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('produksi_packagings', [
            'meter_awal',
            'meter_akhir',
            'meter_pemakaian',
        ]);
    }
}
