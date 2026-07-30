<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Creates dedicated mesin tables in the oroppic (ppic) database.
 * These mirror the orogudang mesin tables but belong to PPIC2 — data
 * entered from the ppic2 context is stored here, not in orogudang.
 */
class CreatePpic2MesinTables extends Migration
{
    protected $DBGroup = 'ppic';

    public function up()
    {
        // ── Mesin Becum ──────────────────────────────────────────────
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk_galon'=> ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'brand'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'grade'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'berat'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'stock'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nomor_lot'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true, 'default' => 'menggiling'],
            'no_spk'      => ['type' => 'INT',     'constraint' => 11,  'null' => true],
            'shif'        => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
            'tanggal'     => ['type' => 'DATE',                          'null' => true],
            'jam'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_mesin'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_produk' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nomor_mesin' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'operator'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'target'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'realisasi'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'revisi'      => ['type' => 'TEXT',                          'null' => true],
            'created_at'  => ['type' => 'DATETIME',                      'null' => true],
            'updated_at'  => ['type' => 'DATETIME',                      'null' => true],
            'deleted_at'  => ['type' => 'DATETIME',                      'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('mesin_becum', true);

        // ── Mesin Powerjet & CCM (produk_cap_galon, warna, packaging) ─
        $capGalonFields = [
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk_cap_galon'=> ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'brand'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'grade'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'warna'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'packaging'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'stock'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_spk'          => ['type' => 'INT',     'constraint' => 11,  'null' => true],
            'shif'            => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
            'tanggal'         => ['type' => 'DATE',                          'null' => true],
            'jam'             => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_mesin'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_produk'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nomor_mesin'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'operator'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'target'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'realisasi'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'revisi'          => ['type' => 'TEXT',                          'null' => true],
            'created_at'      => ['type' => 'DATETIME',                      'null' => true],
            'updated_at'      => ['type' => 'DATETIME',                      'null' => true],
            'deleted_at'      => ['type' => 'DATETIME',                      'null' => true],
        ];

        foreach (['mesin_powerjet1', 'mesin_powerjet2', 'mesin_ccm1', 'mesin_ccm2'] as $table) {
            $this->forge->addField($capGalonFields);
            $this->forge->addKey('id', true);
            $this->forge->createTable($table, true);
        }

        // ── Mesin IPS (produk_preform) ────────────────────────────────
        $ipsFields = [
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk_preform'=> ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'brand'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'grade'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'warna'        => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'stock'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'no_spk'       => ['type' => 'INT',     'constraint' => 11,  'null' => true],
            'shif'         => ['type' => 'VARCHAR', 'constraint' => 50,  'null' => true],
            'tanggal'      => ['type' => 'DATE',                          'null' => true],
            'jam'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_mesin'   => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_produk'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nomor_mesin'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'operator'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'target'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'realisasi'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'revisi'       => ['type' => 'TEXT',                          'null' => true],
            'created_at'   => ['type' => 'DATETIME',                      'null' => true],
            'updated_at'   => ['type' => 'DATETIME',                      'null' => true],
            'deleted_at'   => ['type' => 'DATETIME',                      'null' => true],
        ];

        foreach (['mesin_ips1', 'mesin_ips2', 'mesin_ips3', 'mesin_ips4'] as $table) {
            $this->forge->addField($ipsFields);
            $this->forge->addKey('id', true);
            $this->forge->createTable($table, true);
        }
    }

    public function down()
    {
        foreach ([
            'mesin_becum',
            'mesin_powerjet1', 'mesin_powerjet2',
            'mesin_ccm1', 'mesin_ccm2',
            'mesin_ips1', 'mesin_ips2', 'mesin_ips3', 'mesin_ips4',
        ] as $table) {
            $this->forge->dropTable($table, true);
        }
    }
}
