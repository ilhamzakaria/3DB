<?php

namespace App\Controllers;

use App\Models\MGilinganGalon;

class GilinganGalon extends BaseController
{
    private const INDEX_ROUTE = '/gudang/gilingan_galon';
    private const VIEW_PATH = 'gudang/gilingangalon';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];
    private const SUMBER_OPTIONS = ['supplier', 'produksi'];

    protected $MGilinganGalon;

    public function __construct()
    {
        $this->MGilinganGalon = new MGilinganGalon();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $sumber = trim((string) $this->request->getGet('sumber'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MGilinganGalon;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('nama_gilingan', $q)
                ->orLike('status', $q)
                ->orLike('kode', $q)
                ->orLike('jumlah', $q)
                ->orLike('nomor_lot', $q)
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        if ($sumber !== '' && in_array($sumber, self::SUMBER_OPTIONS, true)) {
            $query = $query->where('sumber_gilingan', $sumber);
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
            'title' => 'Gilingan Galon',
            'data_list' => $data_list,
            'q' => $q,
            'sumber_filter' => $sumber,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_options' => self::STATUS_OPTIONS,
            'sumber_options' => self::SUMBER_OPTIONS,
        ]);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'nama_gilingan' => $this->request->getPost('nama_gilingan'),
            'sumber_gilingan' => $this->request->getPost('sumber_gilingan'),
            'status' => $this->request->getPost('status'),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MGilinganGalon->insert($data)) {
            session()->setFlashdata('success', 'Data gilingan galon berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data gilingan galon');
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
            'nama_gilingan' => $this->request->getPost('nama_gilingan'),
            'sumber_gilingan' => $this->request->getPost('sumber_gilingan'),
            'status' => $this->request->getPost('status'),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MGilinganGalon->update($id, $data)) {
            session()->setFlashdata('success', 'Data gilingan galon berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data gilingan galon');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id ?: $this->request->getPost('id');

        if ($this->MGilinganGalon->delete($id)) {
            session()->setFlashdata('success', 'Data gilingan galon berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data gilingan galon');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }
}
