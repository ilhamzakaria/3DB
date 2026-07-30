<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMesinKomposisiTable extends Migration
{
    protected $DBGroup = 'default';

    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'mesin_module' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'comment'    => 'e.g. becum, ips1, ccm1, ...',
            ],
            'nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['mesin_module', 'nama']);
        $this->forge->createTable('mesin_komposisi', true);
    }

    public function down()
    {
        $this->forge->dropTable('mesin_komposisi', true);
    }
}
