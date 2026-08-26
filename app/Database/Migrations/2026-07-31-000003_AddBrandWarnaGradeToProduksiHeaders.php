<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Add brand, warna, and grade columns to produksi_headers table.
 *
 * These columns are auto-populated from PPIC SPK data when user selects SPK in Produksi form.
 * They are display-only fields (disabled) but submitted as part of the form for historical tracking.
 *
 * Run with: php spark migrate --group produksi
 */
class AddBrandWarnaGradeToProduksiHeaders extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        if (!$this->db->fieldExists('brand', 'produksi_headers')) {
            $this->forge->addColumn('produksi_headers', [
                'brand' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'comment'    => 'Brand from PPIC SPK - auto-filled',
                    'after'      => 'tercapai',
                ],
            ]);
        }

        if (!$this->db->fieldExists('warna', 'produksi_headers')) {
            $this->forge->addColumn('produksi_headers', [
                'warna' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'comment'    => 'Warna from PPIC SPK - auto-filled',
                    'after'      => 'brand',
                ],
            ]);
        }

        if (!$this->db->fieldExists('grade', 'produksi_headers')) {
            $this->forge->addColumn('produksi_headers', [
                'grade' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'comment'    => 'Grade from PPIC SPK - auto-filled',
                    'after'      => 'warna',
                ],
            ]);
        }
    }

    public function down()
    {
        if ($this->db->fieldExists('brand', 'produksi_headers')) {
            $this->forge->dropColumn('produksi_headers', 'brand');
        }
        if ($this->db->fieldExists('warna', 'produksi_headers')) {
            $this->forge->dropColumn('produksi_headers', 'warna');
        }
        if ($this->db->fieldExists('grade', 'produksi_headers')) {
            $this->forge->dropColumn('produksi_headers', 'grade');
        }
    }
}
