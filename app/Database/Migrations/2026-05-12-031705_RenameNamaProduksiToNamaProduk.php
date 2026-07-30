<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameNamaProduksiToNamaProduk extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        if (!$this->db->tableExists('produksi_headers') || !$this->db->fieldExists('nama_produksi', 'produksi_headers')) {
            return;
        }

        $fields = [
            'nama_produksi' => [
                'name' => 'nama_produk',
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('produksi_headers', $fields);
    }

    public function down()
    {
        $fields = [
            'nama_produk' => [
                'name' => 'nama_produksi',
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ];
        $this->forge->modifyColumn('produksi_headers', $fields);
    }
}
