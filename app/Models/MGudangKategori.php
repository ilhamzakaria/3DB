<?php

namespace App\Models;

use CodeIgniter\Model;

class MGudangKategori extends BaseGudangModel
{
    protected $table = 'gudang_item_categories';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'kode',
        'nama',
        'urutan',
    ];
    protected $useTimestamps = true;
}
