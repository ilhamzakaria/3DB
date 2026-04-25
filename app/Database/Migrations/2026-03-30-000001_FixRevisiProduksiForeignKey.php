<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * revisi_produksi.id_produksi holds ppic.id (see Ppic::saveInitialRevisi).
 * The FK was incorrectly created against prod.id, causing inserts to fail.
 */
class FixRevisiProduksiForeignKey extends Migration
{
    public function up()
    {
        $db = $this->db;
        $db->query('ALTER TABLE `revisi_produksi` DROP FOREIGN KEY `fk_revisi_produksi`');
        $db->query(
            'ALTER TABLE `revisi_produksi` ADD CONSTRAINT `fk_revisi_produksi` '
            . 'FOREIGN KEY (`id_produksi`) REFERENCES `ppic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
        );
    }

    public function down()
    {
        $db = $this->db;
        $db->query('ALTER TABLE `revisi_produksi` DROP FOREIGN KEY `fk_revisi_produksi`');
        $db->query(
            'ALTER TABLE `revisi_produksi` ADD CONSTRAINT `fk_revisi_produksi` '
            . 'FOREIGN KEY (`id_produksi`) REFERENCES `prod` (`id`) ON DELETE CASCADE ON UPDATE CASCADE'
        );
    }
}
