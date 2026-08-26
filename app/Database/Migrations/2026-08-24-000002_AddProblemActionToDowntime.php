<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProblemActionToDowntime extends Migration
{
    protected $group = 'produksi';
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Get explicit connection to produksi database
        $db = \Config\Database::connect('produksi');
        
        $fields = [
            'problem' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'action_plant' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ];
        
        // Check if columns already exist
        $table = $db->getFieldData('produksi_downtimes');
        $columns = array_column($table, 'name');
        
        if (!in_array('problem', $columns)) {
            $db->query('ALTER TABLE produksi_downtimes ADD COLUMN problem TEXT NULL AFTER alasan_downtime');
        }
        
        if (!in_array('action_plant', $columns)) {
            $db->query('ALTER TABLE produksi_downtimes ADD COLUMN action_plant TEXT NULL AFTER problem');
        }
    }

    public function down()
    {
        $db = \Config\Database::connect('produksi');
        
        $table = $db->getFieldData('produksi_downtimes');
        $columns = array_column($table, 'name');
        
        if (in_array('problem', $columns)) {
            $db->query('ALTER TABLE produksi_downtimes DROP COLUMN problem');
        }
        
        if (in_array('action_plant', $columns)) {
            $db->query('ALTER TABLE produksi_downtimes DROP COLUMN action_plant');
        }
    }
}
