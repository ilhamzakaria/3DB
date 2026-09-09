<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiMaterial extends BaseProduksiModel
{
    protected $table            = 'produksi_materials';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'produksi_header_id', 'merek_kode', 'pemakaian', 
        'lot_a', 'lot_b', 'lot_c', 'lot_d',
        'komposisi_nama_1', 'komposisi_nama_2', 'komposisi_nama_3', 'komposisi_nama_4',
        'komposisi_persen_1', 'komposisi_persen_2', 'komposisi_persen_3', 'komposisi_persen_4',
        'komposisi_kg_1', 'komposisi_kg_2', 'komposisi_kg_3', 'komposisi_kg_4'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
