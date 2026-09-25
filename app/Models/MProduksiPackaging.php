<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksiPackaging extends BaseProduksiModel
{
    protected $table            = 'produksi_packagings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'produksi_header_id', 'box', 'box_ukuran', 'plastik', 'plastik_bagus', 'plastik_reject', 'plastik_ukuran', 'karung', 'karung_ukuran', 'nicktainer', 'nicktainer_ukuran', 'box_karung_nicktainer',
        'meter_awal', 'meter_akhir', 'meter_pemakaian',
        'ukuran_plastik_besar', 'plastik_besar_awal', 'plastik_besar_akhir', 'plastik_besar_pemakaian',
        'ukuran_plastik_kecil', 'plastik_kecil_awal', 'plastik_kecil_akhir', 'plastik_kecil_pemakaian',
        'ukuran_pe', 'pe_awal', 'pe_akhir', 'pe_pemakaian',
        'ukuran_box_karung', 'box_karung_awal', 'box_karung_akhir', 'box_karung_pemakaian',
        'plastik_awal', 'plastik_akhir', 'material_awal', 'material_akhir'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';
}
