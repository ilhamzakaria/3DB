<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds 'berat' column to mesin_powerjet1/2, mesin_ccm1/2, mesin_ips1-4.
 * Run with --group gudang  → orogudang
 * Run with --group ppic    → oroppic  (same file, DBGroup overridden by --group flag)
 */
class AddBeratToMesinCapGalonIps extends Migration
{
    protected $DBGroup = 'gudang';

    private array $tables = [
        'mesin_powerjet1', 'mesin_powerjet2',
        'mesin_ccm1',      'mesin_ccm2',
        'mesin_ips1',      'mesin_ips2', 'mesin_ips3', 'mesin_ips4',
    ];

    public function up()
    {
        $field = ['berat' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null]];
        foreach ($this->tables as $table) {
            if (!$this->db->fieldExists('berat', $table)) {
                $this->forge->addColumn($table, $field);
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            if ($this->db->fieldExists('berat', $table)) {
                $this->forge->dropColumn($table, 'berat');
            }
        }
    }
}
