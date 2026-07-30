<?php

namespace App\Controllers;

use App\Models\MPackaging;

class Packaging extends BaseController
{
    private const INDEX_ROUTE = '/gudang/packaging';
    private const VIEW_PATH = 'gudang/packaging';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];
    private const PRODUK_OPTIONS = ['BARANG PACKAGING / KEMASAN'];
    private const JENIS_OPTIONS = ['box', 'karung', 'plastik'];
    private const ITEM_OPTIONS = [
        'box' => [
            'Kardus 692x417x503, Cap Galon',
            'Kardus 600X400X300 Cap 5 Gallon, Single',
            'Kardus 600X400X300 Cap 5 Gallon, Single (Return)',
            'Kardus 578 x 540 x 541 Cap 5 Gallon, Single Wall',
            'Kardus 720x550x425 Cap 5 Gallon, Single',
        ],
        'karung' => [
            'Karung 90x130, Karung Cap Gallon',
        ],
        'plastik' => [
            'PE 50x003x85',
            'PE 60x115x008',
            'PE 135 X 110 X 0.03',
        ],
    ];

    protected $MPackaging;

    public function __construct()
    {
        $this->MPackaging = new MPackaging();
        helper('form');
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $jenis = trim((string) $this->request->getGet('jenis'));

        $query = $this->MPackaging;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('jenis_packaging', $q)
                ->orLike('nama_packaging', $q)
                ->orLike('status', $q)
                ->orLike('kode', $q)
                ->orLike('jumlah', $q)
                ->orLike('nomor_lot', $q)
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        if ($jenis !== '' && in_array($jenis, self::JENIS_OPTIONS, true)) {
            $query = $query->where('jenis_packaging', $jenis);
        }

        $packaging = $query
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view(self::VIEW_PATH, [
            'title' => 'Packaging',
            'packaging' => $packaging,
            'q' => $q,
            'jenis_filter' => $jenis,
            'produk_options' => self::PRODUK_OPTIONS,
            'status_options' => self::STATUS_OPTIONS,
            'jenis_options' => self::JENIS_OPTIONS,
            'item_options' => self::ITEM_OPTIONS,
        ]);
    }

    public function packaging()
    {
        return $this->index();
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->normalizeProduk($this->request->getPost('produk')),
            'jenis_packaging' => $this->request->getPost('jenis_packaging'),
            'nama_packaging' => $this->normalizeItem(
                $this->request->getPost('jenis_packaging'),
                $this->request->getPost('nama_packaging')
            ),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MPackaging->insert($data)) {
            session()->setFlashdata('success', 'Data packaging berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data packaging');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function edit($id)
    {
        $packaging = $this->MPackaging->find($id);

        if (!$packaging) {
            session()->setFlashdata('error', 'Data packaging tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($packaging);
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MPackaging->find($id)) {
            session()->setFlashdata('error', 'Data packaging tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->normalizeProduk($this->request->getPost('produk')),
            'jenis_packaging' => $this->request->getPost('jenis_packaging'),
            'nama_packaging' => $this->normalizeItem(
                $this->request->getPost('jenis_packaging'),
                $this->request->getPost('nama_packaging')
            ),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MPackaging->update($id, $data)) {
            session()->setFlashdata('success', 'Data packaging berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data packaging');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MPackaging->find($id)) {
            session()->setFlashdata('error', 'Data packaging tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->MPackaging->delete($id)) {
            session()->setFlashdata('success', 'Data packaging berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data packaging');
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

    private function normalizeProduk(?string $produk): string
    {
        $produk = trim((string) $produk);

        if ($produk === '') {
            return self::PRODUK_OPTIONS[0];
        }

        return $produk;
    }

    private function normalizeItem(?string $jenis, ?string $item): string
    {
        $jenis = $this->normalizeJenis($jenis);
        $item = trim((string) $item);
        $options = self::ITEM_OPTIONS[$jenis] ?? [];

        if ($item === '') {
            return $options[0] ?? '';
        }

        return $item;
    }

    private function normalizeJenis(?string $jenis): string
    {
        $jenis = trim((string) $jenis);

        if ($jenis === '') {
            return self::JENIS_OPTIONS[0];
        }

        return $jenis;
    }
}
