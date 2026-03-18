<?php

namespace App\Models;

use CodeIgniter\Model;

class MAdmin extends Model
{
    protected $table = 'ppic';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_user',
        'jam',
        'no_spk',
        'nama_mesin',
        'nama_produk',
        'shif',
        'operator',
        'target',
        'revisi',
    ];
}
