<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProdukTypeToFinishGood extends Migration
{
    public function up()
    {
        if (!$this->db->fieldExists('produk', 'finish_good')) {
            $this->forge->addColumn('finish_good', [
                'produk' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'id',
                ],
            ]);
        }

        if (!$this->db->fieldExists('type', 'finish_good')) {
            $this->forge->addColumn('finish_good', [
                'type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => true,
                    'after'      => 'produk',
                ],
            ]);
        }

        $this->seedCatatanFinishGood();
    }

    public function down()
    {
        if ($this->db->fieldExists('type', 'finish_good')) {
            $this->forge->dropColumn('finish_good', 'type');
        }

        if ($this->db->fieldExists('produk', 'finish_good')) {
            $this->forge->dropColumn('finish_good', 'produk');
        }
    }

    private function seedCatatanFinishGood(): void
    {
        $rows = $this->parseCatatanRows();

        foreach ($rows as $row) {
            $exists = $this->db->table('finish_good')
                ->where('produk', $row['produk'])
                ->where('type', $row['type'])
                ->where('kode_barang', $row['kode_barang'])
                ->where('kode_warna', $row['kode_warna'])
                ->where('item', $row['item'])
                ->countAllResults();

            if ($exists > 0) {
                continue;
            }

            $this->db->table('finish_good')->insert($row);
        }
    }

    private function parseCatatanRows(): array
    {
        $path = ROOTPATH . 'catatan/catatan.md';

        if (!is_file($path)) {
            return [];
        }

        $rows = [];
        $produk = '';
        $type = '';
        $kodeBarang = '';
        $today = date('Y-m-d');
        $now = date('Y-m-d H:i:s');

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
                $row = $this->parseMarkdownTableRow($rawLine, $produk, $type, $today, $now);

                if ($row !== null) {
                    $rows[] = $row;
                }

                continue;
            }

            $columns = preg_split('/\s{2,}|\t+/', $rawLine, 2);
            $left = trim((string) ($columns[0] ?? ''));
            $item = trim((string) ($columns[1] ?? ''));

            if ($item === '') {
                $item = $left;
                $left = '';
            }

            if ($left !== '' && preg_match('/^\d+$/', $left)) {
                $kodeBarang = $left;
            }

            $item = $this->cleanValue($item);

            if ($produk === '' || $type === '' || $item === '') {
                continue;
            }

            $rows[] = [
                'produk'        => $produk,
                'type'          => $type,
                'kode_barang'   => $kodeBarang !== '' ? $kodeBarang : '-',
                'kode_warna'    => '-',
                'item'          => $item,
                'stok_maximum'  => 0,
                'stok_minimum'  => 0,
                'unit'          => 'pcs',
                'tanggal'       => $today,
                'awal'          => 0,
                'sumber_in'     => 'suplier',
                'out'           => 0,
                'akhir'         => 0,
                'created_at'    => $now,
                'updated_at'    => $now,
            ];
        }

        return $this->uniqueRows($rows);
    }

    private function parseMarkdownTableRow(string $line, string $produk, string $type, string $today, string $now): ?array
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
            'stok_maximum'  => 0,
            'stok_minimum'  => 0,
            'unit'          => 'pcs',
            'tanggal'       => $today,
            'awal'          => 0,
            'sumber_in'     => 'suplier',
            'out'           => 0,
            'akhir'         => 0,
            'created_at'    => $now,
            'updated_at'    => $now,
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
