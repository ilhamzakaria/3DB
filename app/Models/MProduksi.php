<?php

namespace App\Models;

class MProduksi extends BaseProduksiModel
{
    protected $table            = 'produksi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_mesin', 'no_spk', 'tanggal', 'nama_produk', 'batch_number', 'shif', 'nomor_mesin',
        'packing', 'isi', 'cycle_time', 'target', 'operator', 'catatan', 'total_bagus',
        'sisa_po', 'hold', 'gumpalan', 'total_rijek', 'merek_kode', 'm_no_lot1', 'm_pemakaian',
        'c_kode', 'c_no_lot', 'c_pemakaian', 'box', 'plastik', 'm_no_lot2', 'm_no_lot3', 'm_no_lot4',
        'start_up', 'karantina', 'trial', 'camera', 'bottom_putih', 'oval', 'flashing', 'short_shoot',
        'kotor', 'beda_warna', 'sampling_qc', 'kontaminasi', 'black_spot', 'gosong', 'struktur_tdk_std',
        'inject_poin_tdk_std', 'bolong', 'bubble', 'berair', 'neck_panjang', '06-07', '07-08', '08-09',
        '09-10', '10-11', '11-12', '12-13', '13-14', 'hasil_produksi_06_07', 'hasil_produksi_07_08',
        'hasil_produksi_08_09', 'hasil_produksi_09_10', 'hasil_produksi_10_11', 'hasil_produksi_11_12',
        'hasil_produksi_12_13', 'hasil_produksi_13_14', 'id_user'
    ];
}
