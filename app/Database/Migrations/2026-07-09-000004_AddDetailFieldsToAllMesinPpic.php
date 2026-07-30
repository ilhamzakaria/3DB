<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Same as AddDetailFieldsToAllMesin but targets the 'ppic' (oroppic) database.
 */
class AddDetailFieldsToAllMesinPpic extends Migration
{
    protected $DBGroup = 'ppic';

    private array $newFields = [
        'waktu_akhir_target'    => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'target_shift1'         => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'target_shift2'         => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'target_shift3'         => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'waktu_awal_realisasi'  => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'waktu_akhir_realisasi' => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'realisasi_shift1'      => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'realisasi_shift2'      => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'realisasi_shift3'      => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => null],
        'total_spk'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
        'packing'               => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'default' => null],
        'keterangan'            => ['type' => 'TEXT',                          'null' => true, 'default' => null],
    ];

    private array $tables = [
        'mesin_powerjet1', 'mesin_powerjet2',
        'mesin_ccm1',      'mesin_ccm2',
        'mesin_ips1',      'mesin_ips2', 'mesin_ips3', 'mesin_ips4',
    ];

    public function up()
    {
        foreach ($this->tables as $table) {
            foreach ($this->newFields as $col => $def) {
                if (!$this->db->fieldExists($col, $table)) {
                    $this->forge->addColumn($table, [$col => $def]);
                }
            }
        }
    }

    public function down()
    {
        foreach ($this->tables as $table) {
            foreach (array_keys($this->newFields) as $col) {
                if ($this->db->fieldExists($col, $table)) {
                    $this->forge->dropColumn($table, $col);
                }
            }
        }
    }
}
