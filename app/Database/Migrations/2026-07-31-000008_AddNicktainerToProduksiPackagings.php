<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddNicktainerToProduksiPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        $fields = [
            'nicktainer' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'default'    => null,
                'after'      => 'karung_ukuran',
            ],
            'nicktainer_ukuran' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'default'    => null,
                'after'      => 'nicktainer',
            ],
        ];

        $this->forge->addColumn('produksi_packagings', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('produksi_packagings', ['nicktainer', 'nicktainer_ukuran']);
    }
}
