<?php

namespace App\Models;

use CodeIgniter\Model;

class MGudang extends Model
{
    protected $table = 'gudang';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_user',
        'no_spk',
        'jam',
        'shif',
        'bahan_baku',
        'box',
        'karung',
        'plastik',
        'masterbatch',
        'galon_reject_supplier',
        'galon_reject_produksi',
        'gilingan_reject_supplier',
        'gilingan_reject_produksi',
        'stiker',
        'reject_preform',
        'bekuat_pet',
        'bekuan_capgalon',
        'gilingan_screwcap',
        'tanggal',

    ];
}
