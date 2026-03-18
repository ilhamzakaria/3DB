<?php

namespace App\Controllers;

use App\Models\MGudang;

class Gudang extends BaseController
{
    protected $MGudang;

    public function __construct()
    {
        $this->MGudang = new MGudang();
        helper('form');
    }

    public function index()
    {
        $id_user = session()->get('id_user');
        $role = session()->get('role');

        if (!$id_user) {
            return redirect()->to('/login');
        }

        $query = $this->MGudang;

        // Jika bukan ppic, filter berdasarkan id_user
        if ($role !== 'ppic' && $role !== 'produksi' && $role !== 'admin') {
            $query = $query->where('id_user', $id_user);
        }

        $gudang = $query
            ->orderBy('no_spk', 'DESC')
            ->findAll();

        return view('gudang', [
            'title' => 'gudang',
            'gudang' => $gudang
        ]);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/gudang');
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
            'polycarbonate' => $this->request->getPost('polycarbonate'),
            'sisa_po'      => $this->request->getPost('sisa_po'),
            'hold'         => $this->request->getPost('hold'),
            'gumpalan'     => $this->request->getPost('gumpalan'),
            'tanggal'      => $this->request->getPost('tanggal'),
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

        $this->MGudang->save($data);

        return redirect()->to('/gudang')->with('success', 'Data berhasil ditambahkan');
    }

    public function delete($id)
    {
        $this->MGudang->delete($id);
        return redirect()->to('/gudang')->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $data = $this->MGudang->find($id);
        return view('edit', [
            'title' => 'Ubah Data Gudang',
            'data' => $data
        ]);
    }

    public function update($id)
    {
        $id_user = session()->get('id_user');
        $data = [
            'id_user'      => $id_user,
            'no_spk'       => $this->request->getPost('no_spk'),
            'polycarbonate' => $this->request->getPost('polycarbonate'),
            'sisa_po'      => $this->request->getPost('sisa_po'),
            'hold'         => $this->request->getPost('hold'),
            'gumpalan'     => $this->request->getPost('gumpalan'),
            'tanggal'      => $this->request->getPost('tanggal'),
        ];

        $this->MGudang->update($id, $data);
        return redirect()->to('/gudang')->with('success', 'Data berhasil diubah');
    }

    public function exportExcel()
    {
        $data = $this->MGudang->findAll();

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
