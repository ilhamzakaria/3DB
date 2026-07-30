<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDetailFieldsToMesinTables extends Migration
{
    protected $DBGroup = 'gudang';

    // Fields yang sama untuk semua tabel mesin
    private array $newFields = [
        'jam'         => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true, 'default' => null],
        'nama_mesin'  => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true, 'default' => null],
        'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 255,  'null' => true, 'default' => null],
        'nomor_mesin' => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true, 'default' => null],
        'operator'    => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true, 'default' => null],
        'target'      => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true, 'default' => null],
        'realisasi'   => ['type' => 'VARCHAR', 'constraint' => 100,  'null' => true, 'default' => null],
        'revisi'      => ['type' => 'TEXT',                          'null' => true, 'default' => null],
    ];

    // mesin_ccm, powerjet, ips — tambah grade (belum ada di tabel-tabel ini)
    private array $gradeField = [
        'grade' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'default' => null],
    ];

    private array $allTables = [
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

    // Tabel yang belum punya kolom grade
    private array $tablesNeedGrade = [
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
        foreach ($this->allTables as $table) {
            // Tambah grade dulu kalau belum ada
            if (in_array($table, $this->tablesNeedGrade, true)) {
                if (!$this->db->fieldExists('grade', $table)) {
                    $this->forge->addColumn($table, $this->gradeField);
                }
            }

            foreach ($this->newFields as $col => $def) {
                if (!$this->db->fieldExists($col, $table)) {
                    $this->forge->addColumn($table, [$col => $def]);
                }
            }
        }
    }

    public function down()
    {
        $dropCols = array_keys($this->newFields);

        foreach ($this->allTables as $table) {
            foreach ($dropCols as $col) {
                if ($this->db->fieldExists($col, $table)) {
                    $this->forge->dropColumn($table, $col);
                }
            }
            if (in_array($table, $this->tablesNeedGrade, true)) {
                if ($this->db->fieldExists('grade', $table)) {
                    $this->forge->dropColumn($table, 'grade');
                }
            }
        }
    }
}
