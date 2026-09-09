<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameKodeBarangAndNoInvoiceInPembeli extends Migration
{
    protected $DBGroup = 'warehouse';

    public function up()
    {
        $db = \Config\Database::connect($this->DBGroup);
        
        // Check if columns already exist with new names
        $columns = $db->getFieldData('barang_keluar_pembeli');
        $columnNames = array_map(fn($col) => $col->name, $columns);
        
        // Check if columns need to be renamed
        if (in_array('kode_barang', $columnNames)) {
            // Rename kode_barang to nomor_po
            $db->query("ALTER TABLE barang_keluar_pembeli CHANGE COLUMN kode_barang nomor_po VARCHAR(100) NULL DEFAULT NULL");
        }
        
        if (in_array('no_invoice', $columnNames)) {
            // Rename no_invoice to surat_jalan
            $db->query("ALTER TABLE barang_keluar_pembeli CHANGE COLUMN no_invoice surat_jalan VARCHAR(100) NULL DEFAULT NULL");
        }
    }

    public function down()
    {
        $db = \Config\Database::connect($this->DBGroup);
        
        // Check if columns need to be renamed back
        $columns = $db->getFieldData('barang_keluar_pembeli');
        $columnNames = array_map(fn($col) => $col->name, $columns);
        
        // Rename back to old names
        if (in_array('nomor_po', $columnNames)) {
            $db->query("ALTER TABLE barang_keluar_pembeli CHANGE COLUMN nomor_po kode_barang VARCHAR(100) NULL DEFAULT NULL");
        }
        
        if (in_array('surat_jalan', $columnNames)) {
            $db->query("ALTER TABLE barang_keluar_pembeli CHANGE COLUMN surat_jalan no_invoice VARCHAR(100) NULL DEFAULT NULL");
        }
    }
}
