<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinishGoodScrewCapTable extends Migration
{
    protected $DBGroup = 'ppic';

    public function up()
    {
        if ($this->db->tableExists('finish_good_screw_cap')) {
            return;
        }

        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'bagian'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nomor'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'warna'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'berat'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'jumlah'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'keterangan'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'tanggal'     => ['type' => 'DATE', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('finish_good_screw_cap');
    }

    public function down()
    {
        $this->forge->dropTable('finish_good_screw_cap', true);
    }
}
