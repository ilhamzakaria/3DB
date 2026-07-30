<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeleteToMesinTables extends Migration
{
    protected $DBGroup = 'gudang';

    private array $tables = [
        'mesin_becum',
        'mesin_ccm1',
        'mesin_ccm2',
        'mesin_powerjet1',
        'mesin_powerjet2',
        'mesin_ips1',
        'mesin_ips2',
        'mesin_ips3',
        'mesin_ips4',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            if (!$this->db->fieldExists('deleted_at', $table)) {
                $this->forge->addColumn($table, [
                    'deleted_at' => ['type' => 'DATETIME', 'null' => true, 'default' => null],
                ]);
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if ($this->db->fieldExists('deleted_at', $table)) {
                $this->forge->dropColumn($table, 'deleted_at');
            }
        }
    }
}
