<?php

namespace App\Models;

use CodeIgniter\Model;

class MPpic extends Model
{
    protected $table = 'ppic';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_user',
        'no_spk',
        'jam',
        'no_spk',
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
