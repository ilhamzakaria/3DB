<?php

namespace App\Controllers;

use App\Models\MBam;

class Bam extends BaseController
{
    private const INDEX_ROUTE = '/gudang/bam';
    private const VIEW_PATH = 'gudang/bam';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar'];

    protected $MBam;

    public function __construct()
    {
        $this->MBam = new MBam();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MBam;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('tipe', $q)
                ->orLike('nama_bam', $q)
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

        $dataList = $query
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $catalog = $this->getCatalog();
        $types = [];
        $options = [];

        foreach ($catalog as $row) {
            $type = $row['tipe'];

            if (!in_array($type, $types, true)) {
                $types[] = $type;
            }

            $options[$type][] = [
                'item' => $row['item'],
                'kode_barang' => $row['kode_barang'],
                'label' => $row['label'],
            ];
        }

        return view(self::VIEW_PATH, [
            'title' => 'BAM',
            'data_list' => $dataList,
            'bam_catalog' => $catalog,
            'bam_products' => ['BAM'],
            'bam_types' => $types,
            'bam_options' => $options,
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status_options' => self::STATUS_OPTIONS,
        ]);
    }

    private function getCatalog(): array
    {
        $path = ROOTPATH . 'catatan/catatan.md';
        if (!is_file($path)) {
            return [];
        }

        $rows = [];
        $type = '';

        foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $rawLine = trim($line);

            if ($rawLine === '' || str_starts_with($rawLine, '#') || str_starts_with($rawLine, '---')) {
                continue;
            }

            if (preg_match('/^([A-Z ]+)\s*\(BAM\)/i', $rawLine, $matches)) {
                $type = strtoupper(trim($matches[1]));
                continue;
            }

            if (
                $type === ''
                || stripos($rawLine, 'http://') !== false
                || stripos($rawLine, 'buatkan fitur') !== false
                || stripos($rawLine, 'untuk fitur') !== false
                || stripos($rawLine, 'untuk data') !== false
                || stripos($rawLine, 'kode barang') !== false
            ) {
                continue;
            }

            $kodeBarang = '';
            $item = $rawLine;

            if (preg_match('/^(\d+)\s+(.+)$/u', $rawLine, $matches)) {
                $kodeBarang = trim($matches[1]);
                $item = trim($matches[2]);
            }

            $item = trim($item, " \t\n\r\0\x0B|");

            if ($item === '') {
                continue;
            }

            $rows[] = [
                'produk' => 'BAM',
                'tipe' => $type,
                'kode_barang' => $kodeBarang,
                'item' => $item,
                'label' => $kodeBarang !== '' ? $kodeBarang . ' - ' . $item : $item,
            ];
        }

        $unique = [];
        foreach ($rows as $row) {
            $key = implode('|', [$row['tipe'], $row['kode_barang'], $row['item']]);
            $unique[$key] = $row;
        }

        return array_values($unique);
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk') ?: 'BAM',
            'tipe' => $this->request->getPost('tipe'),
            'nama_bam' => $this->request->getPost('nama_bam'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MBam->insert($data)) {
            session()->setFlashdata('success', 'Data BAM berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data BAM');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MBam->find($id)) {
            session()->setFlashdata('error', 'Data BAM tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => $this->request->getPost('produk') ?: 'BAM',
            'tipe' => $this->request->getPost('tipe'),
            'nama_bam' => $this->request->getPost('nama_bam'),
            'status' => $this->normalizeStatus($this->request->getPost('status')),
            'kode' => trim((string) $this->request->getPost('kode')),
            'jumlah' => trim((string) $this->request->getPost('jumlah')),
            'nomor_lot' => trim((string) $this->request->getPost('nomor_lot')),
            'no_spk' => $this->request->getPost('no_spk') !== '' ? (int) $this->request->getPost('no_spk') : null,
            'shif' => trim((string) $this->request->getPost('shif')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($this->MBam->update($id, $data)) {
            session()->setFlashdata('success', 'Data BAM berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data BAM');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MBam->find($id)) {
            session()->setFlashdata('error', 'Data BAM tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->MBam->delete($id)) {
            session()->setFlashdata('success', 'Data BAM berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data BAM');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    private function normalizeStatus(?string $status): string
    {
        $status = trim((string) $status);

        return in_array($status, self::STATUS_OPTIONS, true) ? $status : 'bahan_masuk';
    }
}
