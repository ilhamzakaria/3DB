<?php

namespace App\Controllers;

use App\Models\MRejectProduksi;

class RejectProduksi extends BaseController
{
    private const INDEX_ROUTE = '/gudang/reject_produksi';
    private const VIEW_PATH = 'gudang/rejectproduksi';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];
    private const JENIS_OPTIONS = ['reject_preform', 'bekuan_pet', 'bekuan_cap_galon', 'gilingan_screwcap'];

    protected $MRejectProduksi;

    public function __construct()
    {
        $this->MRejectProduksi = new MRejectProduksi();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $jenis = trim((string) $this->request->getGet('jenis'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MRejectProduksi;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('nama_limbah', $q)
                ->orLike('status', $q)
                ->orLike('kode', $q)
                ->orLike('jumlah', $q)
                ->orLike('nomor_lot', $q)
                ->orLike('no_spk', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        if ($jenis !== '' && in_array($jenis, self::JENIS_OPTIONS, true)) {
            $query = $query->where('jenis_limbah', $jenis);
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
            'title' => 'Reject Produksi / Limbah',
            'data_list' => $data_list,
            'q' => $q,
            'jenis_filter' => $jenis,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_options' => self::STATUS_OPTIONS,
            'jenis_options' => self::JENIS_OPTIONS,
        ]);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk'),
            'nama_limbah' => $this->request->getPost('nama_limbah'),
            'jenis_limbah' => $this->request->getPost('jenis_limbah'),
            'status' => $this->request->getPost('status'),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MRejectProduksi->insert($data)) {
            session()->setFlashdata('success', 'Data reject/limbah berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data reject/limbah');
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
            'nama_limbah' => $this->request->getPost('nama_limbah'),
            'jenis_limbah' => $this->request->getPost('jenis_limbah'),
            'status' => $this->request->getPost('status'),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MRejectProduksi->update($id, $data)) {
            session()->setFlashdata('success', 'Data reject/limbah berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data reject/limbah');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id ?: $this->request->getPost('id');

        if ($this->MRejectProduksi->delete($id)) {
            session()->setFlashdata('success', 'Data reject/limbah berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data reject/limbah');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }
}
