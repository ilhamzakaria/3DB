<?php

namespace App\Models;

use CodeIgniter\Model;

class MPpic extends BasePpicModel
{
    protected $table = 'ppic';
    protected $primaryKey = 'id';

    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'id_user',
        'no_spk',
        'jam',
        'nama_mesin',
        'nama_produk',
        'grade',
        'warna',
        'nomor_mesin',
        'shif',
        'operator',
        'targett',
        'revisi',
        'tanggal',
    ];

    public function getJamByProduksiId($id)
    {
        return $this->where('produksi_id', $id)
            ->findColumn('jam');
    }
}
