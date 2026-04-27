<?php

namespace App\Models;

use CodeIgniter\Model;

class MPackaging extends BaseGudangModel
{
    protected $table = 'packaging';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'produk',
        'nama_packaging',
        'jenis_packaging',
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
