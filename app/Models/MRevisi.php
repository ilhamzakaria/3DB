<?php

namespace App\Models;

use CodeIgniter\Model;

class MRevisi extends Model
{
    protected $table            = 'revisi_produksi';
    protected $primaryKey       = 'id_revisi';
    protected $returnType       = 'array';
    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'id_produksi',
        'revisi_ke',
        'nilai_revisi',
        'keterangan',
        'tanggal_revisi',
    ];

    protected $useTimestamps = false;
}
