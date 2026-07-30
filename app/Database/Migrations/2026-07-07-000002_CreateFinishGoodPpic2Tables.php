<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFinishGoodPpic2Tables extends Migration
{
    protected $DBGroup = 'ppic';

    public function up()
    {
        // Galon: bagian, nomor, nama_produk, grade, berat, wh_oro, wh_bam
        if (!$this->db->tableExists('finish_good_galon')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'bagian'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'nomor'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'grade'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'berat'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'wh_oro'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'wh_bam'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tanggal'     => ['type' => 'DATE', 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('finish_good_galon');
        }

        // Cap Galon: bagian, nomor, nama_produk, warna, berat, jumlah, net
        if (!$this->db->tableExists('finish_good_cap_galon')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'bagian'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'nomor'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'warna'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'berat'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'jumlah'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'net'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tanggal'     => ['type' => 'DATE', 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('finish_good_cap_galon');
        }

        // Freeform: bagian, nomor, nama_produk, warna, berat, jumlah, net
        if (!$this->db->tableExists('finish_good_freeform')) {
            $this->forge->addField([
                'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'bagian'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'nomor'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'warna'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'berat'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'jumlah'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'net'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'tanggal'     => ['type' => 'DATE', 'null' => true],
                'created_at'  => ['type' => 'DATETIME', 'null' => true],
                'updated_at'  => ['type' => 'DATETIME', 'null' => true],
                'deleted_at'  => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->createTable('finish_good_freeform');
        }
    }

    public function down()
    {
        $this->forge->dropTable('finish_good_galon',     true);
        $this->forge->dropTable('finish_good_cap_galon', true);
        $this->forge->dropTable('finish_good_freeform',  true);
    }
}
