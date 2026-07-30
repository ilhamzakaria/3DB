<?php

namespace App\Models;

class MPengiriman extends BaseGudangModel
{
    protected $table = 'pengiriman';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'produk',
        'tipe',
        'nama_bahan',
        'status',
        'kode',
        'jumlah',
        'nomor_lot',
        'no_spk',
        'shif',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
