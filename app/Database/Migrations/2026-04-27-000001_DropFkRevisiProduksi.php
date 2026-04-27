<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DropFkRevisiProduksi extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Drop foreign key constraint that causes issues with cross-database references
        // or missing mirror tables.
        $this->db->query('ALTER TABLE `revisi_produksi` DROP FOREIGN KEY `fk_revisi_produksi`');
    }

    public function down()
    {
        // To restore, we would need to know which table it was referencing.
        // Based on FixRevisiProduksiForeignKey, it was 'ppic'.
        $this->db->query(
            'ALTER TABLE `revisi_produksi` ADD CONSTRAINT `fk_revisi_produksi` '
            . 'FOREIGN KEY (`id_produksi`) REFERENCES `ppic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
        );
    }
}
