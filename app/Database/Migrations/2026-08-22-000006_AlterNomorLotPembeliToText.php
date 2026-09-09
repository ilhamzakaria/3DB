<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterNomorLotPembeliToText extends Migration
{
    protected $DBGroup = 'warehouse';

    public function up(): void
    {
        $this->forge->modifyColumn('barang_keluar_pembeli', [
            'nomor_lot' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->modifyColumn('barang_keluar_pembeli', [
            'nomor_lot' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
            ],
        ]);
    }
}
