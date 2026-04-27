<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiReject extends BaseProduksiModel
{
    protected $table            = 'produksi_rejects';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'produksi_header_id', 'produksi_jam_id', 'jenis_reject', 'jumlah'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
