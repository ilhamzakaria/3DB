<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRejectTypesTable extends Migration
{
    protected $DBGroup = 'produksi';
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'nama_reject' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'unique' => true,
            ],
            'deskripsi' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'is_active' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 1,
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
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('is_active');
        $this->forge->createTable('reject_types');

        // Insert default reject types (20 jenis reject standard)
        $defaultTypes = [
            'Start up',
            'Karantina',
            'Trial',
            'Camera',
            'Bottom Putih',
            'Oval',
            'Flashing',
            'short shoot',
            'Kotor',
            'beda warna',
            'Sampling QC',
            'Kontaminasi',
            'Black spot',
            'Gosong',
            'Struktur tdk std',
            'Inject poin tdk std',
            'Bolong',
            'Bubble',
            'Berair',
            'Neck panjang',
        ];

        $data = [];
        foreach ($defaultTypes as $type) {
            $data[] = [
                'nama_reject' => $type,
                'deskripsi' => null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];
        }

        $this->db->table('reject_types')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('reject_types', true);
    }
}
