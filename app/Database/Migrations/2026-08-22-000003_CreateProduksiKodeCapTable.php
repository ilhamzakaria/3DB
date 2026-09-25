<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProduksiKodeCapTable extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'constraint'     => 20,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'produksi_header_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'FK to produksi_headers.id',
            ],
            // jam_ke: 2, 4, 6, 8
            'jam_ke' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
                'null'       => false,
                'comment'    => 'Jam ke- (2, 4, 6, 8)',
            ],
            // kode_cap_1 .. kode_cap_12
            'kode_cap_1'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_2'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_3'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_4'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_5'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_6'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_7'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_8'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_9'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_10' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_11' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            'kode_cap_12' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
            // runner = jumlah kode cap yang terisi (dihitung dari 12 kolom)
            'runner' => [
                'type'       => 'TINYINT',
                'constraint' => 2,
                'unsigned'   => true,
                'null'       => true,
                'default'    => 0,
                'comment'    => 'Jumlah kode cap yang terisi (1-12)',
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('produksi_header_id');
        $this->forge->addKey(['produksi_header_id', 'jam_ke'], false, false);
        $this->forge->createTable('produksi_kode_cap', true);
    }

    public function down()
    {
        $this->forge->dropTable('produksi_kode_cap', true);
    }
}
