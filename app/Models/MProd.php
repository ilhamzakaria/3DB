<?php

namespace App\Models;

use CodeIgniter\Model;

class MProd extends Model
{
    protected $table = 'prod';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_user',
        'jam',
        'hasil_produksi',
        'no_spk',
        'nama_mesin',
        'nama_produk',
        'shif',
        'operator',
        'tanggal',

    ];

    // protected $useTimestamps = true; // untuk created_at & updated_at
}
