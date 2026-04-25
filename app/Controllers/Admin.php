<?php

namespace App\Controllers;

use App\Models\MProduksi;
use App\Models\MGudang;
use App\Models\MPpic;

class Admin extends BaseController
{
    protected $MProduksi;
    protected $MGudang;
    protected $MPpic;

    public function __construct()
    {
        $this->MProduksi = new MProduksi();
        $this->MGudang = new MGudang();
        $this->MPpic = new MPpic();
        helper('form');
    }

    public function index()
    {
        $data = $this->MPpic
            ->select('ppic.*, prod.hasil_produksi, gudang.bahan_baku, gudang.box, gudang.karung')
            ->join(
                'prod',
                'TRIM(prod.no_spk) = TRIM(ppic.no_spk) AND TRIM(prod.jam) = TRIM(ppic.jam) AND prod.tanggal = ppic.tanggal',
                'left'
            )
            ->join('gudang', 'TRIM(gudang.no_spk) = TRIM(ppic.no_spk)', 'left')
            ->orderBy('ppic.tanggal', 'DESC')
            ->orderBy('ppic.shif', 'ASC')
            ->orderBy('ppic.jam', 'ASC')
            ->findAll();

        return view('admin', [
            'title' => 'Data PPIC, Produksi & Gudang',
            'data' => $data
        ]);
    }

    public function tambahData()
    {
        // Validasi sederhana (opsional tapi bagus)
        if (!$this->request->is('post')) {
            return redirect()->to('/admin');
        }

        //jam produksi
        $jam = $this->request->getPost('jam');
        $data['jam'] = $jam;

        $data = [
            'id_user'      => session()->get('id_user'),
            'jam'          => $this->request->getPost('jam'),
            'no_spk'       => $this->request->getPost('no_spk'),
            'tanggal'      => $this->request->getPost('tanggal'),
            'nama_mesin'   => $this->request->getPost('nama_mesin'),
            'nama_produk'  => $this->request->getPost('nama_produk'),
            'shift'         => $this->request->getPost('shift'),
            'operator'     => $this->request->getPost('operator'),
            'target'       => $this->request->getPost('target'),
            'revisi'       => $this->request->getPost('revisi'),
            'tanggal'      => $this->request->getPost('tanggal')
        ];

        // Mapping reject dinamis ke kolom database
        if (!empty($jenis)) {
            for ($i = 0; $i < count($jenis); $i++) {
                if (!empty($jenis[$i])) {
                    $data[$jenis[$i]] = $jumlah[$i] ?? 0;
                }
            }
        }



        // Hitung total reject otomatis
        $totalReject = 0;
        if (!empty($jumlah)) {
            foreach ($jumlah as $j) {
                $totalReject += (int)$j;
            }
        }

        $data['total_reject'] = $totalReject;

        // Ambil jam produksi dinamis
        $jenis  = $this->request->getPost('jenis') ?? [];
        $jumlah = $this->request->getPost('jumlah') ?? [];
        $jam    = $this->request->getPost('jam') ?? [];
        $hasil  = $this->request->getPost('hasil_produksi') ?? [];

        if (is_array($jam)) {
            for ($i = 0; $i < count($jam); $i++) {

                if (!empty($jam[$i])) {

                    $data[$jam[$i]] = $jam[$i];

                    $kolomHasil = 'hasil_produksi_' . str_replace('-', '_', $jam[$i]);

                    $data[$kolomHasil] = $hasil[$i] ?? 0;
                }
            }
        }

        $this->MProduksi->save($data);

        return redirect()->to('/admin')->with('success', 'Data berhasil ditambahkan');
    }

    public function delete($id)
    {
        $this->MProduksi->delete($id);
        $this->MGudang->delete($id);

        return redirect()->to('/admin')->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $data = $this->MProduksi->find($id);
        return view('edit', [
            'title' => 'Ubah Data Produksi',
            'data' => $data
        ]);
    }

    public function update($id)
    {
        $data = [
            'no_spk'       => $this->request->getPost('no_spk'),
            'tanggal'      => $this->request->getPost('tanggal'),
            'nama_mesin'   => $this->request->getPost('nama_mesin'),
            'nama_produk'  => $this->request->getPost('nama_produk'),
            'batch_number' => $this->request->getPost('batch_number'),
            'shift'        => $this->request->getPost('shift'),
            'nomor_mesin'  => $this->request->getPost('nomor_mesin'),
            'packing'  => $this->request->getPost('packing'),
            'isi'          => $this->request->getPost('isi'),
            'cycle_time'   => $this->request->getPost('cycle_time'),
            'target'       => $this->request->getPost('target'),
            'operator'     => $this->request->getPost('operator'),
            'total_bagus' => $this->request->getPost('total_bagus'),
            'total_reject' => $this->request->getPost('total_reject'),
            'sisa_po' => $this->request->getPost('sisa_po'),
            'hold' => $this->request->getPost('hold'),
            'gumpalan' => $this->request->getPost('gumpalan'),
            'catatan' => $this->request->getPost('catatan'),
            'start_up' => $this->request->getPost('start_up'),
            'karantina' => $this->request->getPost('karantina'),
            'trial' => $this->request->getPost('trial'),
            'camera' => $this->request->getPost('camera'),
            'bottom_putih' => $this->request->getPost('bottom_putih'),
            'oval' => $this->request->getPost('oval'),
            'flashing' => $this->request->getPost('flashing'),
            'short_shoot' => $this->request->getPost('short_shoot'),
            'kotor' => $this->request->getPost('kotor'),
            'beda_warna' => $this->request->getPost('beda_warna'),
            'sampling_qc' => $this->request->getPost('sampling_qc'),
            'kontaminasi' => $this->request->getPost('kontaminasi'),
            'black_spot' => $this->request->getPost('black_spot'),
            'gosong' => $this->request->getPost('gosong'),
            'struktur_tdk_std' => $this->request->getPost('struktur_tdk_std'),
            'inject_poin_tdk_std' => $this->request->getPost('inject_poin_tdk_std'),
            'bolong' => $this->request->getPost('bolong'),
            'bubble' => $this->request->getPost('bubble'),
            'berair' => $this->request->getPost('berair'),
            'neck_panjang' => $this->request->getPost('neck_panjang'),
            '06-07' => $this->request->getPost('06-07'),
            '07-08' => $this->request->getPost('07-08'),
            '08-09' => $this->request->getPost('08-09'),
            '09-10' => $this->request->getPost('09-10'),
            '10-11' => $this->request->getPost('10-11'),
            '11-12' => $this->request->getPost('11-12'),
            '12-13' => $this->request->getPost('12-13'),
            '13-14' => $this->request->getPost('13-14'),
            'hasil_produksi_06_07' => $this->request->getPost('hasil_produksi_06_07'),
            'hasil_produksi_07_08' => $this->request->getPost('hasil_produksi_07_08'),
            'hasil_produksi_08_09' => $this->request->getPost('hasil_produksi_08_09'),
            'hasil_produksi_09_10' => $this->request->getPost('hasil_produksi_09_10'),
            'hasil_produksi_10_11' => $this->request->getPost('hasil_produksi_10_11'),
            'hasil_produksi_11_12' => $this->request->getPost('hasil_produksi_11_12'),
            'hasil_produksi_12_13' => $this->request->getPost('hasil_produksi_12_13'),
            'hasil_produksi_13_14' => $this->request->getPost('hasil_produksi_13_14'),
            'merek_kode' => $this->request->getPost('merek_kode'),
            'm_pemakaian' => $this->request->getPost('m_pemakaian'),
            'm_no_lot1' => $this->request->getPost('m_no_lot1'),
            'm_no_lot2' => $this->request->getPost('m_no_lot2'),
            'm_no_lot3' => $this->request->getPost('m_no_lot3'),
            'm_no_lot4' => $this->request->getPost('m_no_lot4'),
            'c_kode' => $this->request->getPost('c_kode'),
            'c_pemakaian' => $this->request->getPost('c_pemakaian'),
            'c_no_lot' => $this->request->getPost('c_no_lot'),
            'box' => $this->request->getPost('box'),
            'plastik' => $this->request->getPost('plastik')
        ];

        $this->MProduksi->update($id, $data);
        return redirect()->to('/admin')->with('success', 'Data berhasil diubah');
    }

    public function exportExcel()
    {
        $data = $this->MProduksi->findAll();

        // Bersihkan output buffer (penting!)
        if (ob_get_length()) {
            ob_end_clean();
        }

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=data_produksi.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        echo "<tr>
                <th>No SPK</th>
                <th>Tanggal</th>
                <th>Nama Mesin</th>
                <th>Nama Produk</th>
                <th>Batch Number</th>
                <th>Shift</th>
                <th>Nomor Mesin</th>
                <th>Packing Isi</th>
                <th>Cycle Time</th>
                <th>Target</th>
                <th>Operator</th>
            </tr>";

        foreach ($data as $d) {
            echo "<tr>
                    <td>" . $d['no_spk'] . "</td>
                    <td>" . $d['tanggal'] . "</td>
                    <td>" . $d['nama_mesin'] . "</td>
                    <td>" . $d['nama_produk'] . "</td>
                    <td>" . $d['batch_number'] . "</td>
                    <td>" . $d['shift'] . "</td>
                    <td>" . $d['nomor_mesin'] . "</td>
                    <td>" . $d['packing_isi'] . "</td>
                    <td>" . $d['cycle_time'] . "</td>
                    <td>" . $d['target'] . "</td>
                    <td>" . $d['operator'] . "</td>
                </tr>";
        }

        echo "</table>";
        exit;
    }
}
