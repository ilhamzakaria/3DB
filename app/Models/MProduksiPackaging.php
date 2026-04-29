<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiPackaging extends BaseProduksiModel
{
    protected $table            = 'produksi_packagings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'produksi_header_id', 'box', 'box_ukuran', 'plastik', 'plastik_ukuran', 'karung', 'karung_ukuran', 'box_karung_nicktainer'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
