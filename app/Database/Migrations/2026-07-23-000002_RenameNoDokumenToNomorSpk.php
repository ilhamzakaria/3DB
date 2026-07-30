<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameNoDokumenToNomorSpk extends Migration
{
    protected $DBGroup = 'warehouse';

    public function up()
    {
        // Rename no_dokumen to nomor_spk in barang_masuk_pemasok
        $this->db->query('ALTER TABLE barang_masuk_pemasok CHANGE COLUMN no_dokumen nomor_spk VARCHAR(50) NOT NULL UNIQUE');

        // Rename no_dokumen to nomor_spk in barang_masuk_produksi
        $this->db->query('ALTER TABLE barang_masuk_produksi CHANGE COLUMN no_dokumen nomor_spk VARCHAR(50) NOT NULL UNIQUE');

        // Rename no_dokumen to nomor_spk in barang_keluar_pembeli
        $this->db->query('ALTER TABLE barang_keluar_pembeli CHANGE COLUMN no_dokumen nomor_spk VARCHAR(50) NOT NULL UNIQUE');
    }

    public function down()
    {
        // Revert: rename nomor_spk back to no_dokumen in barang_masuk_pemasok
        $this->db->query('ALTER TABLE barang_masuk_pemasok CHANGE COLUMN nomor_spk no_dokumen VARCHAR(50) NOT NULL UNIQUE');

        // Revert: rename nomor_spk back to no_dokumen in barang_masuk_produksi
        $this->db->query('ALTER TABLE barang_masuk_produksi CHANGE COLUMN nomor_spk no_dokumen VARCHAR(50) NOT NULL UNIQUE');

        // Revert: rename nomor_spk back to no_dokumen in barang_keluar_pembeli
        $this->db->query('ALTER TABLE barang_keluar_pembeli CHANGE COLUMN nomor_spk no_dokumen VARCHAR(50) NOT NULL UNIQUE');
    }
}

