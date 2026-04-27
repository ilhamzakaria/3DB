<?php

namespace App\Models;

use CodeIgniter\Model;

class MGudangDetail extends BaseGudangModel
{
    protected $table = 'gudang_item_details';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'gudang_id',
        'kategori_id',
        'nilai',
    ];
    protected $useTimestamps = true;
}
