<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Merges the legacy 'ppic2' table data into the main 'ppic' table,
 * then drops the 'ppic2' table.
 *
 * The ppic and ppic2 tables share an identical schema (same columns).
 * Any rows in ppic2 that do not already exist in ppic (matched by no_spk
 * + tanggal + shif + jam) are inserted into ppic.
 * The ppic2 table is then dropped so the codebase has a single source
 * of truth.
 */
class MergePpic2IntoPpic extends Migration
{
    protected $DBGroup = 'ppic';

    public function up(): void
    {
        // Nothing to do if ppic2 was never created
        if (!$this->db->tableExists('ppic2')) {
            return;
        }

        // Columns shared between ppic and ppic2
        $cols = [
            'id_user', 'no_spk', 'jam', 'nama_mesin', 'nama_produk',
            'grade', 'warna', 'nomor_mesin', 'shif', 'operator',
            'targett', 'revisi', 'tanggal',
            'target_awal', 'target_akhir',
            'realisasi_awal', 'realisasi_akhir',
            'created_at', 'updated_at', 'deleted_at',
        ];

        // Only include columns that exist in ppic2
        $existingCols = array_filter($cols, fn($c) => $this->db->fieldExists($c, 'ppic2'));
        $colList       = implode(', ', $existingCols);

        // Insert rows from ppic2 that are not already in ppic
        // Deduplication key: no_spk + tanggal + shif + jam
        $sql = "
            INSERT INTO ppic ($colList)
            SELECT $colList
            FROM ppic2 src
            WHERE NOT EXISTS (
                SELECT 1 FROM ppic dst
                WHERE dst.no_spk  = src.no_spk
                  AND dst.tanggal = src.tanggal
                  AND dst.shif    = src.shif
                  AND dst.jam     = src.jam
            )
        ";

        $this->db->query($sql);

        // Drop the now-redundant ppic2 table
        $this->forge->dropTable('ppic2', true);
    }

    public function down(): void
    {
        // Recreate ppic2 as an empty table with the same schema as ppic
        // (data cannot be recovered without a backup)
        if ($this->db->tableExists('ppic2')) {
            return;
        }

        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'id_user'          => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'no_spk'           => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'jam'              => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nama_mesin'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_produk'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'grade'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'warna'            => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nomor_mesin'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'shif'             => ['type' => 'VARCHAR', 'constraint' => 10,  'null' => true],
            'operator'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'targett'          => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'revisi'           => ['type' => 'TEXT', 'null' => true],
            'tanggal'          => ['type' => 'DATE', 'null' => true],
            'target_awal'      => ['type' => 'DATETIME', 'null' => true],
            'target_akhir'     => ['type' => 'DATETIME', 'null' => true],
            'realisasi_awal'   => ['type' => 'DATETIME', 'null' => true],
            'realisasi_akhir'  => ['type' => 'DATETIME', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
            'deleted_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ppic2');
    }
}
