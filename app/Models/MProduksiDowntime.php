<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiDowntime extends BaseProduksiModel
{
    protected $table            = 'produksi_downtimes';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'produksi_header_id', 'alasan_downtime', 
        'waktu_mulai', 'waktu_selesai', 'durasi_menit'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
