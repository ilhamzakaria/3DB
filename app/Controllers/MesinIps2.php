<?php

namespace App\Controllers;

use App\Models\MMesinIps2;

class MesinIps2 extends BaseController
{
    private const INDEX_ROUTE = '/gudang/mesin_ips2';
    private const VIEW_PATH = 'gudang/mesinips2';

    protected $MMesin;

    public function __construct()
    {
        $this->MMesin = new MMesinIps2();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MMesin;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk_preform', $q)
                ->orLike('brand', $q)
                ->orLike('warna', $q)
                ->orLike('stock', $q)
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        if (!empty($startDate)) {
            $query = $query->where('tanggal >=', $startDate);
        }

        if (!empty($endDate)) {
            $query = $query->where('tanggal <=', $endDate);
        }

        $data_list = $query
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view(self::VIEW_PATH, [
            'title' => 'Mesin IPS 2',
            'data_list' => $data_list,
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk_preform' => $this->request->getPost('produk_preform'),
            'brand' => $this->request->getPost('brand'),
            'warna' => $this->request->getPost('warna'),
            'stock' => $this->request->getPost('stock'),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => $this->request->getPost('shif'),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MMesin->insert($data)) {
            session()->setFlashdata('success', 'Data mesin ips2 berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id ?: $this->request->getPost('id');

        $data = [
            'produk_preform' => $this->request->getPost('produk_preform'),
            'brand' => $this->request->getPost('brand'),
            'warna' => $this->request->getPost('warna'),
            'stock' => $this->request->getPost('stock'),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => $this->request->getPost('shif'),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MMesin->update($id, $data)) {
            session()->setFlashdata('success', 'Data mesin ips2 berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id ?: $this->request->getPost('id');

        if ($this->MMesin->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }
}
