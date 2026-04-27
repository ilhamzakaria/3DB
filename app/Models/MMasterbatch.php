<?php

namespace App\Models;

use CodeIgniter\Model;

class MMasterbatch extends BaseGudangModel
{
    protected $table = 'masterbatch';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'produk',
        'nama_masterbatch',
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
