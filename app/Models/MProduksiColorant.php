<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiColorant extends Model
{
    protected $table            = 'produksi_colorants';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'produksi_header_id', 'code', 'nomor_lot', 'pemakaian'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
