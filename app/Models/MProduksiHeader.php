<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiHeader extends Model
{
    protected $table            = 'produksi_headers';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_spk', 'nama_mesin', 'nama_produksi', 'batch_number', 'shift', 'grup', 
        'nomor_mesin', 'packing', 'cycle_time', 'target', 'isi', 'tanggal', 
        'operator', 'catatan', 'grand_total_bagus', 'grand_total_reject', 
        'total_downtime', 'sisa_po', 'hold', 'gumpalan',
        'ttd_shift_1', 'ttd_shift_2', 'ttd_shift_3', 'ttd_spv'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
