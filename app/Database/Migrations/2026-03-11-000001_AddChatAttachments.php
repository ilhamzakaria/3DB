<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddChatAttachments extends Migration
{
    public function up()
    {
        $fields = [
            'attachment_path' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'attachment_name' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'attachment_mime' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => true,
            ],
            'attachment_size' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
            ],
        ];

        $this->forge->addColumn('chat_messages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('chat_messages', [
            'attachment_path',
            'attachment_name',
            'attachment_mime',
            'attachment_size',
        ]);
    }
}
