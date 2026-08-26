<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddGumpalanDecimalToProduksiHeaders extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Fix kolom gumpalan dari decimal(25,0) ke decimal(10,2)
        // Jika kolom sudah ada, drop dulu baru buat ulang
        if ($this->db->fieldExists('gumpalan', 'produksi_headers')) {
            // Kolom sudah ada, ubah strukturnya
            $this->forge->modifyColumn('produksi_headers', [
                'gumpalan' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                    'null'       => true,
                ],
            ]);
        } else {
            // Kolom belum ada, buat baru
            $this->forge->addColumn('produksi_headers', [
                'gumpalan' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'default'    => 0.00,
                    'null'       => true,
                    'after'      => 'total_downtime',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('gumpalan', 'produksi_headers')) {
            $this->forge->dropColumn('produksi_headers', 'gumpalan');
        }
    }
}
