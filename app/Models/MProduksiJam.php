<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiJam extends BaseProduksiModel
{
    protected $table            = 'produksi_jam';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'produksi_header_id', 'rentang_jam', 'hasil_produksi', 
        'total_bagus', 'sisa_po', 'hold', 'gumpalan', 'total_reject'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
