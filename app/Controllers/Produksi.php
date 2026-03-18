<?php

namespace App\Controllers;

use App\Models\MProd;

class Produksi extends BaseController
{
    protected $MProd;

    public function __construct()
    {
        $this->MProd = new MProd();
        helper('form');
    }

    public function index()
    {
        $id_user = session()->get('id_user');
        $role = session()->get('role');

        if (!$id_user) {
            return redirect()->to('/login');
        }

        $query = $this->MProd;

        // Jika bukan ppic, filter berdasarkan id_user
        if ($role !== 'ppic' && $role !== 'produksi' && $role !== 'admin' && $role !== 'gudang') {
            $query = $query->where('id_user', $id_user);
        }

        $produksi = $query
            ->orderBy('tanggal', 'DESC')->orderBy('shif', 'DESC')
            ->findAll();

        return view('produksi', [
            'title' => 'produksi',
            'produksi' => $produksi
        ]);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to('/produksi');
        }

        $id_user = session()->get('id_user');
        if (!$id_user) {
            return redirect()->to('/login');
        }

        $jenis  = $this->request->getPost('jenis_reject');
        $jumlah = $this->request->getPost('jumlah_reject');

        // Data utama
        $data = [
            'id_user'      => $id_user,
            'jam'          => $this->request->getPost('jam'),
            'hasil_produksi' => $this->request->getPost('hasil_produksi'),
            'no_spk'       => $this->request->getPost('no_spk'),
            'nama_mesin'   => $this->request->getPost('nama_mesin'),
            'nama_produk'  => $this->request->getPost('nama_produk'),
            'shif'         => $this->request->getPost('shif'),
            'operator'     => $this->request->getPost('operator'),
            'target'       => $this->request->getPost('target'),
            'tanggal'      => $this->request->getPost('tanggal'),
        ];

        // Mapping reject dinamis ke kolom database
        if (!empty($jenis)) {
            // pastikan kita punya array, karena getPost bisa mengembalikan string ketika hanya satu nilai
            $jenisArr = is_array($jenis) ? $jenis : [$jenis];
            $jumlahArr = is_array($jumlah) ? $jumlah : [$jumlah];

            foreach ($jenisArr as $idx => $jns) {
                if (!empty($jns)) {
                    $data[$jns] = $jumlahArr[$idx] ?? 0;
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
            // pastikan array agar count() tidak dilemparkan string
            $jamArr   = is_array($jam) ? $jam : [$jam];
            $hasilArr = is_array($hasil) ? $hasil : [$hasil];

            foreach ($jamArr as $idx => $j) {
                if (!empty($j)) {
                    // Simpan kolom jam (misal: 06-07)
                    $data[$j] = $j;

                    // Ubah 06-07 jadi 06_07 untuk kolom hasil
                    $kolomHasil = 'hasil_produksi_' . str_replace('-', '_', $j);
                    $data[$kolomHasil] = $hasilArr[$idx] ?? 0;
                }
            }
        }

        $this->MProd->save($data);

        return redirect()->to('/produksi')->with('success', 'Data berhasil ditambahkan');
    }

    public function delete($id)
    {
        $this->MProd->delete($id);
        return redirect()->to('/produksi')->with('success', 'Data berhasil dihapus');
    }

    public function edit($id)
    {
        $data = $this->MProd->find($id);
        return view('edit', [
            'title' => 'Ubah Data Produksi',
            'data' => $data
        ]);
    }

    public function update($id)
    {
        $data = [
            'jam'          => $this->request->getPost('jam'),
            'hasil_produksi' => $this->request->getPost('hasil_produksi'),
            'no_spk'       => $this->request->getPost('no_spk'),
            'nama_mesin'   => $this->request->getPost('nama_mesin'),
            'nama_produk'  => $this->request->getPost('nama_produk'),
            'shif'         => $this->request->getPost('shif'),
            'operator'     => $this->request->getPost('operator'),
            'target'       => $this->request->getPost('target'),
            // 'revisi'       => $this->request->getPost('revisi'),
            'tanggal'      => $this->request->getPost('tanggal'),
        ];

        $this->MProd->update($id, $data);
        return redirect()->to('/produksi')->with('success', 'Data berhasil diubah');
    }

    public function exportExcel()
    {
        $data = $this->MProd->findAll();

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
                    <td>" . $d['shif'] . "</td>
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
