<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGudangRelationalTables extends Migration
{
    public function up()
    {
        if (!$this->hasTable('gudang') || $this->hasTable('gudang_item_categories')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'kode' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'nama' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
            ],
            'urutan' => [
                'type' => 'INT',
                'constraint' => 5,
                'default' => 0,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('gudang_item_categories', true);

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'gudang_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
            'kategori_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
            ],
            'nilai' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['gudang_id', 'kategori_id']);
        $this->forge->addForeignKey('gudang_id', 'gudang', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('kategori_id', 'gudang_item_categories', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('gudang_item_details', true);

        $now = date('Y-m-d H:i:s');
        $categories = [
            ['kode' => 'bahan_baku', 'nama' => 'Bahan Baku', 'urutan' => 1],
            ['kode' => 'box', 'nama' => 'Box', 'urutan' => 2],
            ['kode' => 'karung', 'nama' => 'Karung', 'urutan' => 3],
            ['kode' => 'plastik', 'nama' => 'Plastik', 'urutan' => 4],
            ['kode' => 'masterbatch', 'nama' => 'Masterbatch', 'urutan' => 5],
            ['kode' => 'galon_reject_supplier', 'nama' => 'Galon Reject Supplier', 'urutan' => 6],
            ['kode' => 'galon_reject_produksi', 'nama' => 'Galon Reject Produksi', 'urutan' => 7],
            ['kode' => 'gilingan_reject_supplier', 'nama' => 'Gilingan Reject Supplier', 'urutan' => 8],
            ['kode' => 'gilingan_reject_produksi', 'nama' => 'Gilingan Reject Produksi', 'urutan' => 9],
            ['kode' => 'stiker', 'nama' => 'Stiker', 'urutan' => 10],
            ['kode' => 'reject_preform', 'nama' => 'Reject Preform', 'urutan' => 11],
            ['kode' => 'bekuat_pet', 'nama' => 'Bekuat PET', 'urutan' => 12],
            ['kode' => 'bekuan_capgalon', 'nama' => 'Bekuan Capgalon', 'urutan' => 13],
            ['kode' => 'gilingan_screwcap', 'nama' => 'Gilingan Screwcap', 'urutan' => 14],
        ];

        foreach ($categories as &$row) {
            $row['created_at'] = $now;
            $row['updated_at'] = $now;
        }
        unset($row);

        $this->db->table('gudang_item_categories')->insertBatch($categories);
    }

    public function down()
    {
        if ($this->hasTable('gudang_item_details')) {
            $this->forge->dropTable('gudang_item_details', true);
        }

        if ($this->hasTable('gudang_item_categories')) {
            $this->forge->dropTable('gudang_item_categories', true);
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
}
