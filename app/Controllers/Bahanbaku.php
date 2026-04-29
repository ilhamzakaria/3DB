<?php

namespace App\Controllers;

use App\Models\MBahanBaku;

class BahanBaku extends BaseController
{
    private const INDEX_ROUTE = '/gudang/bahan_baku';
    private const VIEW_PATH = 'gudang/bahanbaku';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];

    protected $MBahanBaku;

    public function __construct()
    {
        $this->MBahanBaku = new MBahanBaku();
        helper('form');
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MBahanBaku;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->like('nama_bahan', $q)
                ->orLike('status', $q)
                ->orLike('kode', $q)
                ->orLike('jumlah', $q)
                ->orLike('nomor_lot', $q)
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        if (!empty($startDate)) {
            $query = $query->where('DATE(created_at) >=', $startDate);
        }

        if (!empty($endDate)) {
            $query = $query->where('DATE(created_at) <=', $endDate);
        }

        $bahanBaku = $query
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view(self::VIEW_PATH, [
            'title' => 'Bahan Baku',
            'bahan_baku' => $bahanBaku,
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_options' => self::STATUS_OPTIONS,
        ]);
    }

    public function bahan_baku()
    {
        return $this->index();
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'nama_bahan' => $this->request->getPost('nama_bahan'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => (int) $this->request->getPost('no_spk'),
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MBahanBaku->insert($data)) {
            session()->setFlashdata('success', 'Data bahan baku berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data bahan baku');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function edit($id)
    {
        $bahanBaku = $this->MBahanBaku->find($id);

        if (!$bahanBaku) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($bahanBaku);
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MBahanBaku->find($id)) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'nama_bahan' => $this->request->getPost('nama_bahan'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => (int) $this->request->getPost('no_spk'),
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MBahanBaku->update($id, $data)) {
            session()->setFlashdata('success', 'Data bahan baku berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data bahan baku');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MBahanBaku->find($id)) {
            session()->setFlashdata('error', 'Data bahan baku tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->MBahanBaku->delete($id)) {
            session()->setFlashdata('success', 'Data bahan baku berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data bahan baku');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    private function normalizeStatus(?string $status): string
    {
        $status = trim((string) $status);

        if (!in_array($status, self::STATUS_OPTIONS, true)) {
            return self::STATUS_OPTIONS[0];
        }

        return $status;
    }

}
