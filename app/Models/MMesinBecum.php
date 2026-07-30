<?php

namespace App\Models;

class MMesinBecum extends BaseGudangModel
{
    protected $table      = 'mesin_becum';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'produk_galon', 'brand', 'grade', 'berat', 'stock', 'nomor_lot', 'status',
        'no_spk', 'shif', 'tanggal',
        // waktu & jumlah target
        'jam',                  // waktu awal target
        'waktu_akhir_target',
        'target_shift1', 'target_shift2', 'target_shift3',
        // waktu & jumlah realisasi
        'waktu_awal_realisasi', 'waktu_akhir_realisasi',
        'realisasi_shift1', 'realisasi_shift2', 'realisasi_shift3',
        // extra shifts (JSON array of {label, target} objects)
        'extra_target_shifts',
        // info tambahan
        'total_spk', 'packing', 'keterangan', 'selesai_spk',
        // legacy/shared fields
        'nama_mesin', 'nama_produk', 'nomor_mesin', 'operator',
        'target', 'realisasi', 'revisi',
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $createdField   = 'created_at';
    protected $updatedField   = 'updated_at';
    protected $deletedField   = 'deleted_at';
}
