<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePlantProduksiTables extends Migration
{
    public function up()
    {
        // 1. produksi_headers
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'nomor_spk' => ['type' => 'VARCHAR', 'constraint' => 50],
            'nama_mesin' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'nama_produksi' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'batch_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'shift' => ['type' => 'INT', 'constraint' => 1, 'null' => true],
            'grup' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'nomor_mesin' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'packing' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'cycle_time' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'target' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'isi' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'tanggal' => ['type' => 'DATE', 'null' => true],
            'operator' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'catatan' => ['type' => 'TEXT', 'null' => true],
            'grand_total_bagus' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'grand_total_reject' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_downtime' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'ttd_shift_1' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ttd_shift_2' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ttd_shift_3' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'ttd_spv' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('nomor_spk');
        $this->forge->createTable('produksi_headers');

        // 2. produksi_jam
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produksi_header_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'rentang_jam' => ['type' => 'VARCHAR', 'constraint' => 20],
            'hasil_produksi' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_bagus' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'sisa_po' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'hold' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'gumpalan' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'total_reject' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produksi_header_id', 'produksi_headers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('produksi_jam');

        // 3. produksi_rejects
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produksi_header_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'produksi_jam_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'jenis_reject' => ['type' => 'VARCHAR', 'constraint' => 100],
            'jumlah' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produksi_header_id', 'produksi_headers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('produksi_rejects');

        // 4. produksi_materials
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produksi_header_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'merek_kode' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pemakaian' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'lot_a' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'lot_b' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'lot_c' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'lot_d' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produksi_header_id', 'produksi_headers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('produksi_materials');

        // 5. produksi_colorants
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produksi_header_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'code' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'nomor_lot' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'pemakaian' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => 0.00],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produksi_header_id', 'produksi_headers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('produksi_colorants');

        // 6. produksi_packagings
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produksi_header_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'box_karung_nicktainer' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'plastik' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produksi_header_id', 'produksi_headers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('produksi_packagings');

        // 7. produksi_downtimes
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produksi_header_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'alasan_downtime' => ['type' => 'TEXT', 'null' => true],
            'waktu_mulai' => ['type' => 'DATETIME', 'null' => true],
            'waktu_selesai' => ['type' => 'DATETIME', 'null' => true],
            'durasi_menit' => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('produksi_header_id', 'produksi_headers', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('produksi_downtimes');
    }

    public function down()
    {
        $this->forge->dropTable('produksi_downtimes');
        $this->forge->dropTable('produksi_packagings');
        $this->forge->dropTable('produksi_colorants');
        $this->forge->dropTable('produksi_materials');
        $this->forge->dropTable('produksi_rejects');
        $this->forge->dropTable('produksi_jam');
        $this->forge->dropTable('produksi_headers');
    }
}
