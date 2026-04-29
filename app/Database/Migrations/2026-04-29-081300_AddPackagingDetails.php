<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPackagingDetails extends Migration
{
    public function up()
    {
        $fields = [
            'box'            => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'produksi_header_id'],
            'box_ukuran'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'box'],
            'plastik_ukuran' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'plastik'],
            'karung'         => ['type' => 'INT', 'constraint' => 11, 'default' => 0, 'after' => 'plastik_ukuran'],
            'karung_ukuran'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'karung'],
        ];
        $this->forge->addColumn('produksi_packagings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_packagings', ['box', 'box_ukuran', 'plastik_ukuran', 'karung', 'karung_ukuran']);
    }
}
