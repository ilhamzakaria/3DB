<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePenimbanganJamTable extends Migration
{
    protected $DBGroup = 'master';

    public function up()
    {
        /**
         * Table: penimbangan_jam
         * Purpose: Store timed weighing data (galon & flash) for each hour (1-8) of production operations
         * 
         * Structure:
         * - id: Primary key
         * - produksi_header_id: Foreign key to produksi_headers table (oroproduksi database)
         * - hour: Hour number (1-8)
         * - galon: Weight/quantity for galon (decimal in kg)
         * - flash: Weight/quantity for flash (decimal in kg)
         * - created_at, updated_at: Timestamps
         * 
         * Relationships:
         * - Many penimbangan_jam belong to One produksi_headers record
         * - Used for Becum, Powerjet1, Powerjet2 machines only
         */

        $this->forge->addField([
            'id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'produksi_header_id' => [
                'type' => 'BIGINT',
                'constraint' => 20,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Foreign key to produksi_headers.id',
            ],
            'hour' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'unsigned' => true,
                'null' => false,
                'comment' => 'Hour number (1-8)',
            ],
            'galon' => [
                'type' => 'DECIMAL',
                'constraint' => [8, 2],
                'default' => 0,
                'null' => true,
                'comment' => 'Galon weight in kg',
            ],
            'flash' => [
                'type' => 'DECIMAL',
                'constraint' => [8, 2],
                'default' => 0,
                'null' => true,
                'comment' => 'Flash weight in kg',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('produksi_header_id');
        $this->forge->addKey(['produksi_header_id', 'hour'], false, false); // Composite index for faster lookups
        
        $this->forge->createTable('penimbangan_jam', true);

        // Note: Foreign key constraint NOT added because produksi_headers is in different database (oroproduksi)
        // The relationship is maintained at application level
    }

    public function down()
    {
        $this->forge->dropTable('penimbangan_jam', true);
    }
}
