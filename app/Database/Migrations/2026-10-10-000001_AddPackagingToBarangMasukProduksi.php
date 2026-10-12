<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPackagingToBarangMasukProduksi extends Migration
{
    protected $DBGroup = 'warehouse';

    public function up()
    {
        // Add packaging column if it doesn't exist
        $fields = $this->db->getFieldNames('barang_masuk_produksi');
        if (!in_array('packaging', $fields)) {
            $this->forge->addColumn('barang_masuk_produksi', [
                'packaging' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'nama_barang_4',
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('barang_masuk_produksi');
        if (in_array('packaging', $fields)) {
            $this->forge->dropColumn('barang_masuk_produksi', 'packaging');
        }
    }
}
