<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Adds production-detail columns to mesin_becum.
 * Run twice: once with --group gudang, once with --group ppic
 *
 * The DBGroup is set dynamically so this same file works for both.
 * CI4 MigrationRunner passes the correct db/forge for the group flag.
 */
class AddDetailFieldsToBecum extends Migration
{
    // Override default group so spark migrate (no --group) targets gudang.
    // When run with --group ppic the runner replaces $this->db / $this->forge.
    protected $DBGroup = 'gudang';

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

    public function up()
    {
        foreach ($this->newFields as $col => $def) {
            if (!$this->db->fieldExists($col, 'mesin_becum')) {
                $this->forge->addColumn('mesin_becum', [$col => $def]);
            }
        }
    }

    public function down()
    {
        foreach (array_keys($this->newFields) as $col) {
            if ($this->db->fieldExists($col, 'mesin_becum')) {
                $this->forge->dropColumn('mesin_becum', $col);
            }
        }
    }
}
