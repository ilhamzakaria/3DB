<?php

namespace App\Models;

class MFinishGoodGalon extends BasePpicModel
{
    protected $table      = 'finish_good_galon';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'bagian', 'nomor', 'nama_produk', 'grade', 'berat', 'wh_oro', 'wh_bam', 'tanggal',
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
