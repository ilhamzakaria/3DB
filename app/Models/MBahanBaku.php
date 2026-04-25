<?php

namespace App\Models;

use CodeIgniter\Model;

class MBahanBaku extends Model
{
    protected $table = 'bahan_baku';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'produk',
        'nama_bahan',
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
