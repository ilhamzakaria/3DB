<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add selesai_spk column to produksi_headers table in the Produksi database.
 *
 * This column is set to 1 when PPIC marks the corresponding SPK as "selesai"
 * from any mesin module, enabling automatic status synchronisation between
 * the PPIC mesin tables and the production reporting module.
 *
 * Run with: php spark migrate --group produksi
 */
class AddSelesaiSpkToProduksiHeaders extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        if (!$this->db->fieldExists('selesai_spk', 'produksi_headers')) {
            $this->forge->addColumn('produksi_headers', [
                'selesai_spk' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'unsigned'   => true,
                    'null'       => false,
                    'default'    => 0,
                    'comment'    => '1 = SPK marked selesai by PPIC; 0 = ongoing',
                    'after'      => 'gumpalan',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('selesai_spk', 'produksi_headers')) {
            $this->forge->dropColumn('produksi_headers', 'selesai_spk');
        }
    }
}
