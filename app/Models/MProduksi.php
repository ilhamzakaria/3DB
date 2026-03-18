<?php

namespace App\Models;

use CodeIgniter\Model;

class MProduksi extends Model
{
    protected $table = 'produksi';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id_user',
        'jam',
        'hasil_produksi',
        'no_spk',
        'nama_mesin',
        'nama_produk',
        'shift',
        'operator',
        'target',
        'tanggal',
        // 'id_user',
        // 'no_spk',
        // 'tanggal',
        // 'nama_mesin',
        // 'nama_produk',
        // 'batch_number',
        // 'shift',
        // 'nomor_mesin',
        // 'packing',
        // 'isi',
        // 'cycle_time',
        // 'target',
        // 'operator',
        // 'total_bagus',
        // 'sisa_po',
        // 'hold',
        // 'total_rijek',
        // 'gumpalan',
        // 'catatan',
        // 'start_up',
        // 'karantina',
        // 'trial',
        // 'camera',
        // 'bottom_putih',
        // 'oval',
        // 'flashing',
        // 'short_shoot',
        // 'kotor',
        // 'beda_warna',
        // 'sampling_qc',
        // 'kontaminasi',
        // 'black_spot',
        // 'gosong',
        // 'struktur_tdk_std',
        // 'inject_poin_tdk_std',
        // 'bolong',
        // 'bubble',
        // 'berair',
        // 'neck_panjang',
        // '06-07',
        // '07-08',
        // '08-09',
        // '09-10',
        // '10-11',
        // '11-12',
        // '12-13',
        // '13-14',
        // 'hasil_produksi_06_07',
        // 'hasil_produksi_07_08',
        // 'hasil_produksi_08_09',
        // 'hasil_produksi_09_10',
        // 'hasil_produksi_10_11',
        // 'hasil_produksi_11_12',
        // 'hasil_produksi_12_13',
        // 'hasil_produksi_13_14',

        // material
        // 'merek_kode',
        // 'm_pemakaian',
        // 'm_no_lot1',
        // 'm_no_lot2',
        // 'm_no_lot3',
        // 'm_no_lot4',

        // colorant
        // 'c_kode',
        // 'c_pemakaian',
        // 'c_no_lot',

        // packaging
        // 'box',
        // 'plastik',
    ];

    // protected $useTimestamps = true; // untuk created_at & updated_at
}
