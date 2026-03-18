<?php

namespace App\Controllers;

use App\Models\MProduksi;

class Operator extends BaseController
{
    protected $MProduk;

    public function __construct()
    {
        $this->MProduk = new MProduksi();
        helper('form');
    }

    public function index()
    {
        $id_user = session()->get('id_user');

        if (!$id_user) {
            return redirect()->to('/login');
        }

        $produksi = $this->MProduk
            ->where('id_user', $id_user)
            ->orderBy('tanggal', 'DESC')
            ->findAll();

        return view('operator', [
            'title' => 'operator',
            'produksi' => $produksi
        ]);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/operator');
        }

        $id_user = session()->get('id_user');
        if (!$id_user) {
            return redirect()->to('/login');
        }

        // Ambil reject dinamis
        $jenis  = $this->request->getPost('jenis_reject');
        $jumlah = $this->request->getPost('jumlah_reject');

        // Data utama
        $data = [
            'id_user'      => $id_user,
            'no_spk'       => $this->request->getPost('no_spk'),
            'tanggal'      => $this->request->getPost('tanggal'),
            'nama_mesin'   => $this->request->getPost('nama_mesin'),
            'nama_produk'  => $this->request->getPost('nama_produk'),
            'batch_number' => $this->request->getPost('batch_number'),
            'shift'        => $this->request->getPost('shift'),
            'nomor_mesin'  => $this->request->getPost('nomor_mesin'),
            'packing'      => $this->request->getPost('packing'),
            'isi'          => $this->request->getPost('isi'),
            'cycle_time'   => $this->request->getPost('cycle_time'),
            'target'       => $this->request->getPost('target'),
            'operator'     => $this->request->getPost('operator'),
            'total_bagus'  => $this->request->getPost('total_bagus'),
            'sisa_po'      => $this->request->getPost('sisa_po'),
            'hold'         => $this->request->getPost('hold'),
            'gumpalan'     => $this->request->getPost('gumpalan'),
            'catatan'      => $this->request->getPost('catatan'),
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

        $data['total_rijek'] = $totalReject;

        // Ambil jam produksi dinamis
        $jam   = $this->request->getPost('jam');
        $hasil = $this->request->getPost('hasil_produksi');

        if (!empty($jam)) {
            for ($i = 0; $i < count($jam); $i++) {

                if (!empty($jam[$i])) {

                    // Simpan kolom jam (misal: 06-07)
                    $data[$jam[$i]] = $jam[$i];

                    // Ubah 06-07 jadi 06_07 untuk kolom hasil
                    $kolomHasil = 'hasil_produksi_' . str_replace('-', '_', $jam[$i]);

                    $data[$kolomHasil] = $hasil[$i] ?? 0;
                }
            }
        }

        $this->MProduk->save($data);

        return redirect()->to('/operator')->with('success', 'Data berhasil ditambahkan');
    }

    public function delete($id)
    {
        $this->MProduk->delete($id);
        return redirect()->to('/operator')->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $data = $this->MProduk->find($id);
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
            'total_rijek' => $this->request->getPost('total_rijek'),
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

        $this->MProduk->update($id, $data);
        return redirect()->to('/operator')->with('success', 'Data berhasil diubah');
    }

    public function exportExcel()
    {
        $data = $this->MProduk->findAll();

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
