<?php

namespace App\Controllers;

use App\Models\MProduk;

class Dashboard extends BaseController
{
    protected $MProduk;

    public function __construct()
    {
        $this->MProduk = new MProduk();
        helper('form');
    }

    public function index()
    {
        $produksi = $this->MProduk->findAll();

        return view('dashboard', [
            'title' => 'Dashboard',
            'produksi' => $produksi
        ]);
    }

    public function tambahData()
    {
        // Validasi sederhana (opsional tapi bagus)
        if (!$this->request->is('post')) {
            return redirect()->to('/dashboard');
        }

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
        ];

        $this->MProduk->save($data); // bisa pakai insert() atau save()

        return redirect()->to('/dashboard')->with('success', 'Data berhasil ditambahkan');
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
    // public function exportExcel()
    // {
    //     dd('masuk export');
    // }
}
