<?php

namespace App\Controllers;

use App\Models\MStikerGudang;

class Stiker extends BaseController
{
    private const INDEX_ROUTE = '/gudang/stiker';
    private const VIEW_PATH = 'gudang/stiker';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];

    protected $MStiker;

    public function __construct()
    {
        $this->MStiker = new MStikerGudang();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MStiker;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('nama_stiker', $q)
                ->orLike('status', $q)
                ->orLike('kode', $q)
                ->orLike('jumlah', $q)
                ->orLike('nomor_lot', $q)
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
            'title' => 'Stiker',
            'data_list' => $data_list,
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_options' => self::STATUS_OPTIONS,
        ]);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'nama_stiker' => $this->request->getPost('nama_stiker'),
            'status' => $this->request->getPost('status'),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MStiker->insert($data)) {
            session()->setFlashdata('success', 'Data stiker berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data stiker');
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
            'produk' => $this->request->getPost('produk'),
            'nama_stiker' => $this->request->getPost('nama_stiker'),
            'status' => $this->request->getPost('status'),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MStiker->update($id, $data)) {
            session()->setFlashdata('success', 'Data stiker berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data stiker');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id ?: $this->request->getPost('id');

        if ($this->MStiker->delete($id)) {
            session()->setFlashdata('success', 'Data stiker berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data stiker');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }
}
