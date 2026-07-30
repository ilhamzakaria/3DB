<?php

namespace App\Models;

use CodeIgniter\Model;

class MFinishGood extends BaseGudangModel
{
    protected $table = 'finish_good';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'produk',
        'type',
        'kode_barang',
        'kode_warna',
        'item',
        'stok_maximum',
        'stok_minimum',
        'unit',
        'tanggal',
        'awal',
        'sumber_in',
        'out',
        'akhir',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
