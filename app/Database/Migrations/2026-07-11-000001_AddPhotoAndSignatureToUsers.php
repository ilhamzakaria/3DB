<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoAndSignatureToUsers extends Migration
{
    protected $DBGroup = 'master';

    public function up()
    {
        // Cek apakah tabel users sudah ada, jika tidak maka buat
        if (!$this->db->tableExists('users')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'username' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => false,
                    'unique'     => true,
                ],
                'password' => [
                    'type' => 'TEXT',
                    'null' => false,
                ],
                'role' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
                'foto_profile' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'comment'    => 'Path to profile photo - stored in public/uploads/profile/',
                ],
                'tanda_tangan' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                    'comment'    => 'Path to signature image - stored in public/uploads/ttd/',
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
            $this->forge->createTable('users');
        } else {
            // Jika tabel sudah ada, hanya tambahkan kolom baru jika belum ada
            $fields = $this->db->getFieldData('users');
            $existingColumns = array_column($fields, 'name');

            if (!in_array('foto_profile', $existingColumns)) {
                $this->forge->addColumn('users', [
                    'foto_profile' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 255,
                        'null'       => true,
                        'after'      => 'role',
                        'comment'    => 'Path to profile photo',
                    ],
                ]);
            }

            if (!in_array('tanda_tangan', $existingColumns)) {
                $this->forge->addColumn('users', [
                    'tanda_tangan' => [
                        'type'       => 'VARCHAR',
                        'constraint' => 255,
                        'null'       => true,
                        'after'      => 'foto_profile',
                        'comment'    => 'Path to signature image',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('users')) {
            $fields = $this->db->getFieldData('users');
            $existingColumns = array_column($fields, 'name');

            if (in_array('foto_profile', $existingColumns)) {
                $this->forge->dropColumn('users', 'foto_profile');
            }

            if (in_array('tanda_tangan', $existingColumns)) {
                $this->forge->dropColumn('users', 'tanda_tangan');
            }
        }
    }
}
