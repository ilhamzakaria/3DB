<?php

namespace App\Models;

class MPpicMesinIps1 extends BasePpicMesinModel
{
    protected $table = 'mesin_ips1';

    protected $allowedFields = [
        'produk_preform', 'brand', 'grade', 'warna', 'berat', 'stock',
        'no_spk', 'shif', 'tanggal',
        'jam', 'nama_mesin', 'nama_produk', 'nomor_mesin', 'operator',
        'target', 'realisasi', 'revisi',
        'status', 'extra_target_shifts', 'jam_awal', 'jam_akhir',
        'waktu_akhir_target', 'target_shift1', 'target_shift2', 'target_shift3', 'waktu_awal_realisasi', 'waktu_akhir_realisasi', 'realisasi_shift1', 'realisasi_shift2', 'realisasi_shift3', 'total_spk', 'packing', 'keterangan', 'selesai_spk',
        'created_at', 'updated_at', 'deleted_at',
    ];
}
