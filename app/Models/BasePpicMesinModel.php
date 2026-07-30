<?php

namespace App\Models;

/**
 * Base model for all PPIC mesin tables.
 * Uses the 'ppic' DB group (oroppic database) so data is isolated
 * from the Gudang department's orogudang database.
 */
abstract class BasePpicMesinModel extends BasePpicModel
{
    protected $primaryKey = 'id';

    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';
}
