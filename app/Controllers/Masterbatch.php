<?php

namespace App\Controllers;

use App\Models\MMasterbatch;

class Masterbatch extends BaseController
{
    private const INDEX_ROUTE = '/gudang/masterbatch';
    private const VIEW_PATH = 'gudang/masterbatch';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];

    protected $MMasterbatch;

    public function __construct()
    {
        $this->MMasterbatch = new MMasterbatch();
        helper('form');
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));

        $query = $this->MMasterbatch;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('nama_masterbatch', $q)
                ->orLike('status', $q)
                ->orLike('kode', $q)
                ->orLike('jumlah', $q)
                ->orLike('nomor_lot', $q)
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        $masterbatch = $query
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view(self::VIEW_PATH, [
            'title' => 'Masterbatch',
            'masterbatch' => $masterbatch,
            'q' => $q,
            'status_options' => self::STATUS_OPTIONS,
        ]);
    }

    public function masterbatch()
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
            'nama_masterbatch' => $this->request->getPost('nama_masterbatch'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MMasterbatch->insert($data)) {
            session()->setFlashdata('success', 'Data masterbatch berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data masterbatch');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function edit($id)
    {
        $masterbatch = $this->MMasterbatch->find($id);

        if (!$masterbatch) {
            session()->setFlashdata('error', 'Data masterbatch tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($masterbatch);
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MMasterbatch->find($id)) {
            session()->setFlashdata('error', 'Data masterbatch tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'nama_masterbatch' => $this->request->getPost('nama_masterbatch'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MMasterbatch->update($id, $data)) {
            session()->setFlashdata('success', 'Data masterbatch berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data masterbatch');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MMasterbatch->find($id)) {
            session()->setFlashdata('error', 'Data masterbatch tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->MMasterbatch->delete($id)) {
            session()->setFlashdata('success', 'Data masterbatch berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data masterbatch');
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
