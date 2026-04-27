<?php

namespace App\Models;

use CodeIgniter\Model;

class MGalonReject extends BaseGudangModel
{
    protected $table = 'galon_reject';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'produk',
        'nama_galon_reject',
        'sumber_reject',
        'status',
        'kode',
        'jumlah',
        'nomor_lot',
        'no_spk',
        'shif',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
