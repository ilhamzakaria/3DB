<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPlastikMaterialAwalAkhirToProduksiPackagings extends Migration
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
        
        // Plastik Awal/Akhir (untuk CCM1, CCM2, IPS1-4)
        if (!in_array('plastik_awal', $columnNames)) {
            $fieldsToAdd['plastik_awal'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'comment'    => 'Plastik stok awal (untuk CCM & IPS)',
            ];
        }
        
        if (!in_array('plastik_akhir', $columnNames)) {
            $fieldsToAdd['plastik_akhir'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'comment'    => 'Plastik stok akhir (untuk CCM & IPS)',
            ];
        }
        
        // Material Awal/Akhir (untuk CCM1, CCM2, IPS1-4)
        if (!in_array('material_awal', $columnNames)) {
            $fieldsToAdd['material_awal'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'comment'    => 'Material stok awal (untuk CCM & IPS)',
            ];
        }
        
        if (!in_array('material_akhir', $columnNames)) {
            $fieldsToAdd['material_akhir'] = [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => true,
                'default'    => null,
                'comment'    => 'Material stok akhir (untuk CCM & IPS)',
            ];
        }
        
        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('produksi_packagings', $fieldsToAdd);
        }
    }

    public function down(): void
    {
        $this->forge->dropColumn('produksi_packagings', [
            'plastik_awal',
            'plastik_akhir',
            'material_awal',
            'material_akhir',
        ]);
    }
}
