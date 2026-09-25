<?php

namespace App\Models;

class MProduksiKodeCap extends BaseProduksiModel
{
    protected $table            = 'produksi_kode_cap';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $allowedFields    = [
        'produksi_header_id',
        'jam_ke',
        'kode_cap_1',  'kode_cap_2',  'kode_cap_3',  'kode_cap_4',
        'kode_cap_5',  'kode_cap_6',  'kode_cap_7',  'kode_cap_8',
        'kode_cap_9',  'kode_cap_10', 'kode_cap_11', 'kode_cap_12',
        'runner',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
