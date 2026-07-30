<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePpic2Table extends Migration
{
    protected $DBGroup = 'ppic';

    public function up()
    {
        if ($this->db->tableExists('ppic2')) {
            return;
        }

        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'id_user' => [
                'type' => 'INT',
                'constraint' => 11,
                'default' => 0,
            ],
            'jam' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'no_spk' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'nama_mesin' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'nama_produk' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'grade' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'warna' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'nomor_mesin' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
            ],
            'shif' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'operator' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'targett' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'revisi' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'tanggal' => [
                'type' => 'VARCHAR',
                'constraint' => 55,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'target_awal' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'target_akhir' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'realisasi_awal' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
            'realisasi_akhir' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('ppic2');
    }

    public function down()
    {
        $this->forge->dropTable('ppic2');
    }
}
