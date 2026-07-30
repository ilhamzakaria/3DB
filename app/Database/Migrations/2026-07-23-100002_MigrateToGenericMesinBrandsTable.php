<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MigrateToGenericMesinBrandsTable extends Migration
{
    protected $DBGroup = 'gudang';

    public function up()
    {
        // Rename the old becum-specific table to the new generic one
        $this->db->query("RENAME TABLE mesin_becum_brands TO mesin_brands");

        // Add the mesin_module column to track which mesin this brand belongs to
        $this->forge->addColumn('mesin_brands', [
            'mesin_module' => [
                'type' => 'VARCHAR',
                'constraint' => 30,
                'default' => 'becum',
                'null' => false,
                'after' => 'id',
            ]
        ]);

        // Update all existing rows to have mesin_module = 'becum'
        $this->db->table('mesin_brands')->update(['mesin_module' => 'becum']);

        // Add index on the mesin_module column for faster lookups
        $this->forge->addKey(['mesin_module', 'grade'], false, false, 'idx_module_grade');
    }

    public function down()
    {
        // Remove the index
        $this->forge->dropKey('mesin_brands', 'idx_module_grade');

        // Remove mesin_module column
        $this->forge->dropColumn('mesin_brands', 'mesin_module');

        // Rename back to becum-specific table
        $this->db->query("RENAME TABLE mesin_brands TO mesin_becum_brands");
    }
}
