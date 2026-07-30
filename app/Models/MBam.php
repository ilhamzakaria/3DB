<?php

namespace App\Models;

class MBam extends BaseGudangModel
{
    protected $table = 'bam';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'produk',
        'tipe',
        'nama_bam',
        'status',
        'kode',
        'jumlah',
        'nomor_lot',
        'no_spk',
        'shif',
        'tanggal',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}
