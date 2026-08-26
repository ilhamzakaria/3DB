<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTotalSpkToProduksiHeaders extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Add total_spk column to produksi_headers if it doesn't exist
        if (!$this->db->fieldExists('total_spk', 'produksi_headers')) {
            $this->forge->addColumn('produksi_headers', [
                'total_spk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'target'
                ]
            ]);
        }
    }

    public function down()
    {
        // Remove total_spk column if it exists
        if ($this->db->fieldExists('total_spk', 'produksi_headers')) {
            $this->forge->dropColumn('produksi_headers', 'total_spk');
        }
    }
}
