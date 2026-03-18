<?php

namespace App\Models;

use CodeIgniter\Model;

class MUser extends Model
{
    protected $table = 'users';
    protected $allowedFields = ['username', 'password', 'role'];
}
