<?php

namespace App\Models;

use CodeIgniter\Model;

class MProd extends Model
{
    protected $table = 'prod';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_user',
        'nama_mesin',
        'nama_produk',
        'batch_number',
        'shif',
        'grup',
        'nomor_mesin',
        'packing',
        'isi',
        'cycle_time',
        'target',
        'no_spk',
        'operator',
        'jam',
        'hasil_produksi',
        'tanggal',

    ];

    // protected $useTimestamps = true; // untuk created_at & updated_at
}
