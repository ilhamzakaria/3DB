<?php

namespace App\Models;

use CodeIgniter\Model;

class MUser extends BaseMasterModel
{
    protected $table = 'users';
    protected $allowedFields = ['username', 'password', 'role'];
}
