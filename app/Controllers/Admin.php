<?php

namespace App\Controllers;

use App\Models\MGudang;
use App\Models\MPpic;

class Admin extends BaseController
{
    protected $MGudang;
    protected $MPpic;

    public function __construct()
    {
        $this->MGudang = new MGudang();
        $this->MPpic = new MPpic();
        helper('form');
    }

    public function index()
    {
        $data = $this->MPpic
            ->select('ppic.*, gudang.bahan_baku, gudang.box, gudang.karung')
            ->join('gudang', 'TRIM(gudang.no_spk) = TRIM(ppic.no_spk)', 'left')
            ->orderBy('ppic.tanggal', 'DESC')
            ->orderBy('ppic.shif', 'ASC')
            ->orderBy('ppic.jam', 'ASC')
            ->findAll();

        return view('admin', [
            'title' => 'Data PPIC & Gudang',
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

    public function delete($id)
    {
        $this->MGudang->delete($id);

        return redirect()->to('/admin')->with('success', 'Data berhasil dihapus');
    }
}
