<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGudangKomponenColumns extends Migration
{
    private array $komponenColumns = [
        'bahan_baku' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'box' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'karung' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'plastik' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'masterbatch' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'galon_reject_supplier' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'galon_reject_produksi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'gilingan_reject_supplier' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'gilingan_reject_produksi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'stiker' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'reject_preform' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'bekuat_pet' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'bekuan_capgalon' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        'gilingan_screwcap' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
    ];

    public function up()
    {
        if (!$this->hasTable('gudang')) {
            return;
        }

        $fieldsToAdd = [];
        foreach ($this->komponenColumns as $column => $definition) {
            if (!$this->hasField('gudang', $column)) {
                $fieldsToAdd[$column] = $definition;
            }
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('gudang', $fieldsToAdd);
        }
    }

    public function down()
    {
        if (!$this->hasTable('gudang')) {
            return;
        }

        foreach (array_keys($this->komponenColumns) as $column) {
            if ($this->hasField('gudang', $column)) {
                $this->forge->dropColumn('gudang', $column);
            }
        }
    }

    private function hasTable(string $table): bool
    {
        try {
            $result = $this->db->query('SHOW TABLES LIKE ' . $this->db->escape($table));
            return $result !== false && $result->getNumRows() > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function hasField(string $table, string $field): bool
    {
        try {
            $query = $this->db->query('SHOW COLUMNS FROM `' . $table . '` LIKE ' . $this->db->escape($field));
            return $query !== false && $query->getNumRows() > 0;
        } catch (\Throwable $e) {
            return false;
        }
    }
}
