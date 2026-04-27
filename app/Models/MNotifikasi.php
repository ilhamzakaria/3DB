<?php

namespace App\Models;

use CodeIgniter\Model;

class MNotifikasi extends BaseMasterModel
{
    protected $table = 'notifikasi';
    protected $primaryKey = 'id';
    protected $allowedFields = ['role_tujuan', 'pesan', 'status', 'created_at'];
}
