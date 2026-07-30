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
        'jumlah',
        'revisi',
        'tanggal',
        'target_awal',
        'target_akhir',
        'realisasi_awal',
        'realisasi_akhir',
    ];

    protected $validationRules = [
        'target_awal'     => 'required|valid_date',
        'target_akhir'    => 'required|valid_date|target_akhir_after_target_awal',
        'realisasi_awal'  => 'permit_empty|valid_date',
        'realisasi_akhir' => 'permit_empty|valid_date|realisasi_akhir_after_realisasi_awal|realisasi_awal_after_target_awal',
    ];

    protected $validationMessages = [
        'target_awal' => [
            'required'   => 'Target Awal wajib diisi.',
            'valid_date' => 'Format Target Awal harus berupa tanggal dan jam yang valid.',
        ],
        'target_akhir' => [
            'required'                       => 'Target Akhir wajib diisi.',
            'valid_date'                     => 'Format Target Akhir harus berupa tanggal dan jam yang valid.',
            'target_akhir_after_target_awal' => 'Target Akhir tidak boleh lebih lampau dari Target Awal.',
        ],
        'realisasi_awal' => [
            'valid_date' => 'Format Realisasi Awal harus berupa tanggal dan jam yang valid.',
        ],
        'realisasi_akhir' => [
            'valid_date'                           => 'Format Realisasi Akhir harus berupa tanggal dan jam yang valid.',
            'realisasi_akhir_after_realisasi_awal' => 'Realisasi Akhir tidak boleh lebih lampau dari Realisasi Awal.',
            'realisasi_awal_after_target_awal'     => 'Realisasi Awal tidak boleh dimulai sebelum Target Awal.',
        ],
    ];

    public function getJamByProduksiId($id)
    {
        return $this->where('produksi_id', $id)
            ->findColumn('jam');
    }
}
