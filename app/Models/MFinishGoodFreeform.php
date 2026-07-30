<?php

namespace App\Models;

class MFinishGoodFreeform extends BasePpicModel
{
    protected $table      = 'finish_good_freeform';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'bagian', 'nomor', 'nama_produk', 'warna', 'berat', 'jumlah', 'packing', 'tanggal',
        'created_at', 'updated_at', 'deleted_at',
    ];


    protected $useTimestamps = true;
    protected $useSoftDeletes = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
