<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegrinToBarangMasukProduksi extends Migration
{
    protected $DBGroup = 'warehouse';

    public function up()
    {
        $fields = $this->db->getFieldNames('barang_masuk_produksi');
        if (!in_array('regrin', $fields)) {
            $this->forge->addColumn('barang_masuk_produksi', [
                'regrin' => [
                    'type'       => 'INT',
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'packaging',
                ],
            ]);
        }
    }

    public function down()
    {
        $fields = $this->db->getFieldNames('barang_masuk_produksi');
        if (in_array('regrin', $fields)) {
            $this->forge->dropColumn('barang_masuk_produksi', 'regrin');
        }
    }
}
