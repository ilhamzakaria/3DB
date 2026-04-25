<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGudangModulesBatch2 extends Migration
{
    public function up()
    {
        // Mesin Becum (produk galon, brand, grade, berat, stock)
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk_galon' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'brand' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'grade' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'berat' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'stock' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'no_spk' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'shif' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'tanggal' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('mesin_becum');

        // Mesin Powerjet & CCM (produk cap galon, brand, warna, packaging, stock)
        $mesinCommonFields = [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk_cap_galon' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'brand' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'warna' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'packaging' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'stock' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'no_spk' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'shif' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'tanggal' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $mesinNames = ['mesin_powerjet1', 'mesin_powerjet2', 'mesin_ccm1', 'mesin_ccm2'];
        foreach ($mesinNames as $table) {
            $this->forge->addField($mesinCommonFields);
            $this->forge->addKey('id', true);
            $this->forge->createTable($table);
        }

        // Mesin IPS (produk preform 12,5 gr, brand, warna, stock)
        $ipsFields = [
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'produk_preform' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'brand' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'warna' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => true],
            'stock' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'no_spk' => ['type' => 'INT', 'constraint' => 11, 'null' => true],
            'shif' => ['type' => 'VARCHAR', 'constraint' => '50', 'null' => true],
            'tanggal' => ['type' => 'DATE', 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ];

        $ipsNames = ['mesin_ips1', 'mesin_ips2', 'mesin_ips3', 'mesin_ips4'];
        foreach ($ipsNames as $table) {
            $this->forge->addField($ipsFields);
            $this->forge->addKey('id', true);
            $this->forge->createTable($table);
        }
    }

    public function down()
    {
        $this->forge->dropTable('mesin_becum');
        $this->forge->dropTable('mesin_powerjet1');
        $this->forge->dropTable('mesin_powerjet2');
        $this->forge->dropTable('mesin_ccm1');
        $this->forge->dropTable('mesin_ccm2');
        $this->forge->dropTable('mesin_ips1');
        $this->forge->dropTable('mesin_ips2');
        $this->forge->dropTable('mesin_ips3');
        $this->forge->dropTable('mesin_ips4');
    }
}
