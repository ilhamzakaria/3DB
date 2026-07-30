<?php

namespace App\Controllers;

use App\Models\MFinishGood;

class FinishGood extends BaseController
{
    private const INDEX_ROUTE = '/gudang/finish_good';
    private const VIEW_PATH = 'gudang/finishgood';
    private const SOURCE_OPTIONS = ['suplier', 'produksi'];

    protected $MFinishGood;

    public function __construct()
    {
        $this->MFinishGood = new MFinishGood();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->MFinishGood;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk', $q)
                ->orLike('type', $q)
                ->orLike('kode_barang', $q)
                ->orLike('kode_warna', $q)
                ->orLike('item', $q)
                ->orLike('stok_maximum', $q)
                ->orLike('stok_minimum', $q)
                ->orLike('unit', $q)
                ->orLike('tanggal', $q)
                ->orLike('awal', $q)
                ->orLike('sumber_in', $q)
                ->orLike('out', $q)
                ->orLike('akhir', $q)
                ->groupEnd();
        }

        if (!empty($startDate)) {
            $query = $query->where('tanggal >=', $startDate);
        }

        if (!empty($endDate)) {
            $query = $query->where('tanggal <=', $endDate);
        }

        $finishGood = $query
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        $allOptions = $this->getItemOptions();
        $products = [];
        $types = [];
        $itemOptions = [];

        foreach ($allOptions as $opt) {
            $prod = $opt['produk'];
            $type = $opt['type'];
            
            if (!in_array($prod, $products, true)) {
                $products[] = $prod;
            }
            if (!in_array($type, $types, true)) {
                $types[] = $type;
            }
            
            $itemOptions[$type][] = [
                'item' => $opt['item'],
                'kode_barang' => $opt['kode_barang'],
                'kode_warna' => $opt['kode_warna']
            ];
        }

        return view(self::VIEW_PATH, [
            'title' => 'Finish Good',
            'finish_good' => $finishGood,
            'finish_good_products' => $products,
            'finish_good_types' => $types,
            'finish_good_options' => $itemOptions,
            'q' => $q,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'source_options' => self::SOURCE_OPTIONS,
        ]);
    }

    public function finish_good()
    {
        return $this->index();
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => trim((string) $this->request->getPost('produk')),
            'type' => trim((string) $this->request->getPost('type')),
            'kode_barang' => trim((string) $this->request->getPost('kode_barang')),
            'kode_warna' => trim((string) $this->request->getPost('kode_warna')),
            'item' => trim((string) $this->request->getPost('item')),
            'stok_maximum' => $this->normalizeInt($this->request->getPost('stok_maximum')),
            'stok_minimum' => $this->normalizeInt($this->request->getPost('stok_minimum')),
            'unit' => $this->normalizeUnit($this->request->getPost('unit')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
            'awal' => $this->normalizeInt($this->request->getPost('awal')),
            'sumber_in' => $this->normalizeSource($this->request->getPost('sumber_in')),
            'out' => $this->normalizeInt($this->request->getPost('out')),
            'akhir' => $this->normalizeInt($this->request->getPost('akhir')),
        ];

        if ($this->MFinishGood->insert($data)) {
            session()->setFlashdata('success', 'Data finish good berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data finish good');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function edit($id)
    {
        $finishGood = $this->MFinishGood->find($id);

        if (!$finishGood) {
            session()->setFlashdata('error', 'Data finish good tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($finishGood);
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to(self::INDEX_ROUTE);
        }

        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MFinishGood->find($id)) {
            session()->setFlashdata('error', 'Data finish good tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        $data = [
            'produk' => trim((string) $this->request->getPost('produk')),
            'type' => trim((string) $this->request->getPost('type')),
            'kode_barang' => trim((string) $this->request->getPost('kode_barang')),
            'kode_warna' => trim((string) $this->request->getPost('kode_warna')),
            'item' => trim((string) $this->request->getPost('item')),
            'stok_maximum' => $this->normalizeInt($this->request->getPost('stok_maximum')),
            'stok_minimum' => $this->normalizeInt($this->request->getPost('stok_minimum')),
            'unit' => $this->normalizeUnit($this->request->getPost('unit')),
            'tanggal' => $this->request->getPost('tanggal') ?: date('Y-m-d'),
            'awal' => $this->normalizeInt($this->request->getPost('awal')),
            'sumber_in' => $this->normalizeSource($this->request->getPost('sumber_in')),
            'out' => $this->normalizeInt($this->request->getPost('out')),
            'akhir' => $this->normalizeInt($this->request->getPost('akhir')),
        ];

        if ($this->MFinishGood->update($id, $data)) {
            session()->setFlashdata('success', 'Data finish good berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data finish good');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    public function delete($id = null)
    {
        $id = $id !== null ? (int) $id : (int) $this->request->getPost('id');

        if ($id <= 0 || !$this->MFinishGood->find($id)) {
            session()->setFlashdata('error', 'Data finish good tidak ditemukan');
            return redirect()->to(self::INDEX_ROUTE);
        }

        if ($this->MFinishGood->delete($id)) {
            session()->setFlashdata('success', 'Data finish good berhasil dihapus');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data finish good');
        }

        return redirect()->to(self::INDEX_ROUTE);
    }

    private function normalizeSource(?string $source): string
    {
        $source = trim((string) $source);

        if (!in_array($source, self::SOURCE_OPTIONS, true)) {
            return self::SOURCE_OPTIONS[0];
        }

        return $source;
    }

    private function normalizeUnit(?string $unit): string
    {
        $unit = trim((string) $unit);

        return $unit !== '' ? $unit : 'pcs';
    }

    private function normalizeInt($value): int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return 0;
        }

        return (int) $value;
    }

    private function getItemOptions(): array
    {
        $path = ROOTPATH . 'catatan/catatan.md';
        if (!is_file($path)) {
            return [];
        }

        $rows = [];
        $produk = '';
        $type = '';
        $kodeBarang = '';

        foreach (file($path, FILE_IGNORE_NEW_LINES) ?: [] as $line) {
            $rawLine = trim($line);

            if ($rawLine === '' || str_starts_with($rawLine, '#') || str_starts_with($rawLine, '---')) {
                continue;
            }

            if (preg_match('/^produk\s*:\s*(.+)$/i', $rawLine, $matches)) {
                $produk = $this->cleanValue($matches[1]);
                $type = '';
                $kodeBarang = '';
                continue;
            }

            if (preg_match('/^(type|tye)\s*:\s*(.+)$/i', $rawLine, $matches)) {
                $type = $this->cleanValue($matches[2]);
                $kodeBarang = '';
                continue;
            }

            if (preg_match('/^([A-Za-z][A-Za-z\s]+)\s*:\s*$/', $rawLine, $matches)) {
                $type = $this->cleanValue($matches[1]);
                $kodeBarang = '';
                continue;
            }

            if (stripos($rawLine, 'kode barang') !== false || stripos($rawLine, 'kode brang') !== false) {
                continue;
            }

            if (str_starts_with($rawLine, '|')) {
                $row = $this->parseMarkdownTableRow($rawLine, $produk, $type);
                if ($row !== null) {
                    $rows[] = $row;
                }
                continue;
            }

            $columns = preg_split('/\s{2,}|\t+/', $rawLine);
            
            $left = trim((string) ($columns[0] ?? ''));
            $item = '';
            $kodeWarna = '';
            
            if (count($columns) >= 3) {
                // kode_barang | warna_barang | item
                $kodeWarna = trim((string) $columns[1]);
                $item = trim(implode(' ', array_slice($columns, 2)));
            } elseif (count($columns) === 2) {
                $item = trim((string) $columns[1]);
            } else {
                $item = $left;
                $left = '';
            }

            if ($left !== '' && preg_match('/^\d+$/', $left)) {
                $kodeBarang = $left;
            } elseif ($left !== '') {
                // If it's not numbers, it might just be a string like 'Blue 16', in which case maybe it's not kode_barang.
                // Let's just keep the last kodeBarang if it was empty, or replace it.
                // Looking at data, sometimes kode_barang is empty and the first column is warna_barang.
                if (count($columns) >= 2 && !preg_match('/^\d+$/', $left) && stripos($left, 'Blue') !== false) {
                    $kodeWarna = $left;
                    $left = '';
                } else {
                    $kodeBarang = $left;
                }
            }

            $item = $this->cleanValue($item);

            if ($produk === '' || $type === '' || $item === '') {
                continue;
            }

            $rows[] = [
                'produk'        => $produk,
                'type'          => $type,
                'kode_barang'   => $kodeBarang !== '' ? $kodeBarang : '-',
                'kode_warna'    => $kodeWarna !== '' ? $kodeWarna : '-',
                'item'          => $item,
            ];
        }

        return $this->uniqueRows($rows);
    }

    private function parseMarkdownTableRow(string $line, string $produk, string $type): ?array
    {
        $columns = array_map('trim', explode('|', trim($line, '|')));

        if (count($columns) < 3 || stripos($columns[0], 'kode') !== false || str_contains($columns[0], '---')) {
            return null;
        }

        $kodeBarang = $this->cleanValue($columns[0]);
        $kodeWarna = $this->cleanValue($columns[1]);
        $item = $this->cleanValue($columns[2]);

        if ($produk === '' || $type === '' || $item === '') {
            return null;
        }

        return [
            'produk'        => $produk,
            'type'          => $type,
            'kode_barang'   => $kodeBarang !== '' ? $kodeBarang : '-',
            'kode_warna'    => $kodeWarna !== '' ? $kodeWarna : '-',
            'item'          => $item,
        ];
    }

    private function cleanValue(string $value): string
    {
        return trim($value, " \t\n\r\0\x0B,");
    }

    private function uniqueRows(array $rows): array
    {
        $unique = [];

        foreach ($rows as $row) {
            $key = implode('|', [
                $row['produk'],
                $row['type'],
                $row['kode_barang'],
                $row['kode_warna'],
                $row['item'],
            ]);

            $unique[$key] = $row;
        }

        return array_values($unique);
    }
}
