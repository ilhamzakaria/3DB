<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIpsRejectTypes extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Add 5 new reject types specific to IPS machines
        $newRejectTypes = [
            [
                'nama_reject' => 'Support Ring',
                'deskripsi' => 'Jenis reject untuk support ring pada mesin IPS',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Bottom Bergelombang',
                'deskripsi' => 'Jenis reject untuk bottom yang bergelombang pada mesin IPS',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Residu',
                'deskripsi' => 'Jenis reject untuk residu pada mesin IPS',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Feeling',
                'deskripsi' => 'Jenis reject untuk feeling pada mesin IPS',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Longgate',
                'deskripsi' => 'Jenis reject untuk longgate pada mesin IPS',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('reject_types')->insertBatch($newRejectTypes);
    }

    public function down()
    {
        // Remove the added reject types
        $typesToRemove = [
            'Support Ring',
            'Bottom Bergelombang',
            'Residu',
            'Feeling',
            'Longgate',
        ];

        foreach ($typesToRemove as $type) {
            $this->db->table('reject_types')
                     ->where('nama_reject', $type)
                     ->delete();
        }
    }
}
