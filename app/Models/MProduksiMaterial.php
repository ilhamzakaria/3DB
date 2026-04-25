<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiMaterial extends Model
{
    protected $table            = 'produksi_materials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'produksi_header_id', 'merek_kode', 'pemakaian', 
        'lot_a', 'lot_b', 'lot_c', 'lot_d'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
