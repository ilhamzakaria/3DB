<?php

namespace App\Controllers;

use App\Models\MPengiriman;

class Pengiriman extends BaseController
{
    private const INDEX_ROUTE = '/gudang/pengiriman';
    private const VIEW_PATH = 'gudang/pengiriman';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];
    private const PENGIRIMAN_PRODUCTS = [
        'Galon',
        'BAHAN DALAM PROSES',
        'BAHAN PENOLONG',
        'BARANG PACKAGING / KEMASAN',
    ];
    private const PENGIRIMAN_TYPES = [
        'Virgin',
        'Regrind (gilingan) dari Supplier',
        'Regrind (gilingan) dari PT.Oro Plastindo',
        'Barium',
        'Regrind Cap Gallon',
        'Stock Turunan Bin',
        'Master Batch',
        'Plastik Bungkus',
    ];

    protected $MPengiriman;

    public function __construct()
    {
        $this->MPengiriman = new MPengiriman();
        helper('form');
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MPengiriman;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('tipe', $q)
                ->orLike('nama_bahan', $q)
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

        $pengiriman = $query
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Ambil semua data barang unik dari tabel gudang untuk dropdown Item Barang
        $allItems = $this->getAllGudangItems();

        return view(self::VIEW_PATH, [
            'title' => 'Pengiriman',
            'pengiriman' => $pengiriman,
            'all_items' => $allItems,
            'pengiriman_products' => self::PENGIRIMAN_PRODUCTS,
            'pengiriman_types' => self::PENGIRIMAN_TYPES,
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_options' => self::STATUS_OPTIONS,
        ]);
    }

    private function getAllGudangItems(): array
    {
        $db = $this->dbGudang();
        $items = [];

        $tables = ['bahan_baku', 'packaging', 'masterbatch', 'galon_reject', 'stiker_gudang', 'reject_produksi', 'gilingan_galon'];
        $fieldMap = [
            'bahan_baku' => 'nama_bahan',
            'packaging' => 'nama_packaging',
            'masterbatch' => 'nama_masterbatch',
            'galon_reject' => 'nama_galon_reject',
            'stiker_gudang' => 'nama_stiker',
            'reject_produksi' => 'nama_limbah',
            'gilingan_galon' => 'nama_gilingan',
        ];

        foreach ($tables as $table) {
            if ($db->tableExists($table)) {
                $field = $fieldMap[$table] ?? null;
                if ($field) {
                    $rows = $db->table($table)->select($field)->distinct()->get()->getResultArray();
                    foreach ($rows as $row) {
                        if (!empty($row[$field])) {
                            $items[] = $row[$field];
                        }
                    }
                }
            }
        }

        return array_unique($items);
    }

    public function pengiriman()
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
            'tipe' => $this->request->getPost('tipe'),
            'nama_bahan' => $this->request->getPost('nama_bahan'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => (int) $this->request->getPost('no_spk'),
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MPengiriman->insert($data)) {
            session()->setFlashdata('success', 'Data pengiriman berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data pengiriman');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function edit($id)
    {
        $pengiriman = $this->MPengiriman->find($id);

        if (!$pengiriman) {
            session()->setFlashdata('error', 'Data pengiriman tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($pengiriman);
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MPengiriman->find($id)) {
            session()->setFlashdata('error', 'Data pengiriman tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'tipe' => $this->request->getPost('tipe'),
            'nama_bahan' => $this->request->getPost('nama_bahan'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => (int) $this->request->getPost('no_spk'),
            'shif' => trim((string) $this->request->getPost('shif')),
        ];

        if ($this->MPengiriman->update($id, $data)) {
            session()->setFlashdata('success', 'Data pengiriman berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data pengiriman');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MPengiriman->find($id)) {
            session()->setFlashdata('error', 'Data pengiriman tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->MPengiriman->delete($id)) {
            session()->setFlashdata('success', 'Data pengiriman berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data pengiriman');
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
