<?php

namespace App\Controllers;

use App\Models\MMasterbatch;

class Masterbatch extends BaseController
{
    private const INDEX_ROUTE = '/gudang/masterbatch';
    private const VIEW_PATH = 'gudang/masterbatch';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];
    private const PRODUK_OPTIONS = [
        'BAHAN PENOLONG',
    ];
    private const TIPE_OPTIONS = [
        'Master Batch',
    ];
    private const ITEM_OPTIONS = [
        'Master Batch' => [
            'Renol Blue PC-C7216R',
            'Renol Blue PC-D8997',
            'Renol Violet PC-D87226 MG',
            'Plast Trans Blue MFPC 15023',
            'Plast Trans Violet MFPC 15999 (A)',
            'Plast Green MFPE 20564-22',
            'Lucent Blue 0878-BL LM5300729',
            'Lucent Blue 1761 (A)-BL LM5201494',
            'Lucent Red 0157-BL LM3300115',
            'Lucent Green 1749-BL LM6301490',
            'Lucent Green 5771-BL',
            'Lucent Blue 6019 BL LM5305779',
            'Lucent Blue 6172 (A) LM5106030',
            'Lucent Blue 6420 (A) SWS',
            'Lucent Green 2221-BL LM6301973',
            'Lucent Violet 6407 BL',
            'Finolene Green GSE-1407',
        ],
    ];

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
                ->orLike('tipe', $q)
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
            'produk_options' => self::PRODUK_OPTIONS,
            'status_options' => self::STATUS_OPTIONS,
            'tipe_options' => self::TIPE_OPTIONS,
            'item_options' => self::ITEM_OPTIONS,
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
            'produk' => $this->normalizeProduk($this->request->getPost('produk')),
            'tipe' => $this->normalizeTipe($this->request->getPost('tipe')),
            'nama_masterbatch' => $this->normalizeItem(
                $this->request->getPost('tipe'),
                $this->request->getPost('nama_masterbatch')
            ),
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
            'produk' => $this->normalizeProduk($this->request->getPost('produk')),
            'tipe' => $this->normalizeTipe($this->request->getPost('tipe')),
            'nama_masterbatch' => $this->normalizeItem(
                $this->request->getPost('tipe'),
                $this->request->getPost('nama_masterbatch')
            ),
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

    private function normalizeTipe(?string $tipe): string
    {
        $tipe = trim((string) $tipe);

        if ($tipe === '') {
            return self::TIPE_OPTIONS[0];
        }

        return $tipe;
    }

    private function normalizeProduk(?string $produk): string
    {
        $produk = trim((string) $produk);

        if ($produk === '') {
            return self::PRODUK_OPTIONS[0];
        }

        return $produk;
    }

    private function normalizeItem(?string $tipe, ?string $item): string
    {
        $tipe = $this->normalizeTipe($tipe);
        $item = trim((string) $item);
        $options = self::ITEM_OPTIONS[$tipe] ?? [];

        if ($item === '') {
            return $options[0] ?? '';
        }

        return $item;
    }
}
