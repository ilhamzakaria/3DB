<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBecumRejectTypes extends Migration
{
    protected $DBGroup = 'produksi';

    public function up()
    {
        // Becum-specific reject types (updated)
        $becumRejectTypes = [
            [
                'nama_reject' => 'Start up',
                'deskripsi' => 'Reject tipe Start up untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Blackspot',
                'deskripsi' => 'Reject tipe Blackspot untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Mulut oval',
                'deskripsi' => 'Reject tipe Mulut oval untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Leher bengkok',
                'deskripsi' => 'Reject tipe Leher bengkok untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Emboss penyok',
                'deskripsi' => 'Reject tipe Emboss penyok untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Mata ikan',
                'deskripsi' => 'Reject tipe Mata ikan untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Material',
                'deskripsi' => 'Reject tipe Material untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Garis kasar',
                'deskripsi' => 'Reject tipe Garis kasar untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Berair',
                'deskripsi' => 'Reject tipe Berair untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Bergejolom bang',
                'deskripsi' => 'Reject tipe Bergejolom bang untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Buram',
                'deskripsi' => 'Reject tipe Buram untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Beda warna',
                'deskripsi' => 'Reject tipe Beda warna untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Kontaminasi',
                'deskripsi' => 'Reject tipe Kontaminasi untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Drop test',
                'deskripsi' => 'Reject tipe Drop test untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Bintik',
                'deskripsi' => 'Reject tipe Bintik untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Berat kurang',
                'deskripsi' => 'Reject tipe Berat kurang untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Tidak sempurna',
                'deskripsi' => 'Reject tipe Tidak sempurna untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'nama_reject' => 'Lain-lain',
                'deskripsi' => 'Reject tipe Lain-lain untuk mesin Becum',
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        // Delete all existing reject types
        $this->db->table('reject_types')->truncate();

        // Insert Becum-specific reject types
        $this->db->table('reject_types')->insertBatch($becumRejectTypes);
    }

    public function down()
    {
        // Restore to previous state (empty)
        $this->db->table('reject_types')->truncate();
    }
}
