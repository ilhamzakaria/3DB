<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCounterFieldsToProduksiHeaders extends Migration
{
    public function up()
    {
        $db      = \Config\Database::connect('produksi');
        $columns = $db->getFieldNames('produksi_headers');

        if (!in_array('counter_awal', $columns)) {
            $db->query('ALTER TABLE produksi_headers ADD COLUMN counter_awal BIGINT NULL DEFAULT NULL AFTER sapuan');
        }
        if (!in_array('counter_akhir', $columns)) {
            $db->query('ALTER TABLE produksi_headers ADD COLUMN counter_akhir BIGINT NULL DEFAULT NULL AFTER counter_awal');
        }
        if (!in_array('total_counter', $columns)) {
            $db->query('ALTER TABLE produksi_headers ADD COLUMN total_counter BIGINT NULL DEFAULT NULL AFTER counter_akhir');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect('produksi');
        foreach (['total_counter', 'counter_akhir', 'counter_awal'] as $col) {
            $columns = $db->getFieldNames('produksi_headers');
            if (in_array($col, $columns)) {
                $db->query("ALTER TABLE produksi_headers DROP COLUMN {$col}");
            }
        }
    }
}
