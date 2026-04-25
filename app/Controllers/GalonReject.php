<?php

namespace App\Controllers;

use App\Models\MGalonReject;

class GalonReject extends BaseController
{
    private const INDEX_ROUTE = '/gudang/galon_reject';
    private const VIEW_PATH = 'gudang/galonreject';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];
    private const SUMBER_OPTIONS = ['supplier', 'produksi'];

    protected $MGalonReject;

    public function __construct()
    {
        $this->MGalonReject = new MGalonReject();
        helper('form');
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $sumber = trim((string) $this->request->getGet('sumber'));

        $query = $this->MGalonReject;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('nama_galon_reject', $q)
                ->orLike('status', $q)
                ->orLike('kode', $q)
                ->orLike('jumlah', $q)
                ->orLike('nomor_lot', $q)
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        if ($sumber !== '' && in_array($sumber, self::SUMBER_OPTIONS, true)) {
            $query = $query->where('sumber_reject', $sumber);
        }

        $galon_reject = $query
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view(self::VIEW_PATH, [
            'title' => 'Galon Reject',
            'galon_reject' => $galon_reject,
            'q' => $q,
            'sumber_filter' => $sumber,
            'status_options' => self::STATUS_OPTIONS,
            'sumber_options' => self::SUMBER_OPTIONS,
        ]);
    }

    public function galon_reject()
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
            'nama_galon_reject' => $this->request->getPost('nama_galon_reject'),
            'sumber_reject' => $this->request->getPost('sumber_reject'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MGalonReject->insert($data)) {
            session()->setFlashdata('success', 'Data galon reject berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data galon reject');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function edit($id)
    {
        $galon_reject = $this->MGalonReject->find($id);

        if (!$galon_reject) {
            session()->setFlashdata('error', 'Data galon reject tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($galon_reject);
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MGalonReject->find($id)) {
            session()->setFlashdata('error', 'Data galon reject tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'nama_galon_reject' => $this->request->getPost('nama_galon_reject'),
            'sumber_reject' => $this->request->getPost('sumber_reject'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MGalonReject->update($id, $data)) {
            session()->setFlashdata('success', 'Data galon reject berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data galon reject');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MGalonReject->find($id)) {
            session()->setFlashdata('error', 'Data galon reject tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->MGalonReject->delete($id)) {
            session()->setFlashdata('success', 'Data galon reject berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data galon reject');
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
