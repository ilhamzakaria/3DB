<?php

namespace App\Models;

use CodeIgniter\Model;

abstract class BaseMasterModel extends Model
{
    protected $DBGroup = 'master';
}
