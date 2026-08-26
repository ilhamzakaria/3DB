<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MakePlastikNullableInPackagings extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Ubah kolom plastik menjadi nullable dengan default 0
        // agar tidak error saat form tidak mengirim nilai plastik
        $this->db->query("ALTER TABLE `produksi_packagings` MODIFY `plastik` INT(11) NULL DEFAULT 0");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `produksi_packagings` MODIFY `plastik` INT(11) NOT NULL DEFAULT 0");
    }
}
