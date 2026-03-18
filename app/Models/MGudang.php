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
        'polycarbonate',
        'sisa_po',
        'hold',
        'gumpalan',
        'tanggal',

    ];
}
