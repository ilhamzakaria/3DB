<?php

namespace App\Controllers;

use App\Models\MProd;
use App\Models\MGudang;
use App\Models\MPpic;
use App\Models\MRevisi;

class Home extends BaseController
{
    protected $MProd;
    protected $MGudang;
    protected $MPpic;
    protected $MRevisi;
    protected ?bool $hasRevisiTable = null;

    public function __construct()
    {
        $this->MProd = new MProd();
        $this->MGudang = new MGudang();
        $this->MPpic = new MPpic();
        $this->MRevisi = new MRevisi();
        helper('form');
    }

    public function index()
    {
        return $this->renderHome();
    }

    public function mesin1()
    {
        return $this->renderHome('mesin1');
    }

    public function mesin2()
    {
        return $this->renderHome('mesin2');
    }

    private function renderHome(?string $mesin = null)
    {
        [$ppic, $produksi, $gudang] = $this->getHomeData($mesin);

        return view('home', [
            'title' => 'Data PPIC, Produksi & Gudang',
            'ppic' => $ppic,
            'produksi' => $produksi,
            'gudang' => $gudang,
            'active_mesin' => $mesin,
        ]);
    }

    private function getHomeData(?string $mesin = null): array
    {
        // Data berdasarkan inputan masing-masing departemen (tidak di-join)
        $ppic = $this->MPpic->orderBy('tanggal', 'DESC')->orderBy('shif', 'ASC')->findAll();
        $produksi = $this->MProd->orderBy('tanggal', 'DESC')->orderBy('shif', 'DESC')->findAll();
        $gudang = $this->MGudang->orderBy('tanggal', 'DESC')->findAll();
        $ppic = $this->attachRevisiData($ppic);

        if (empty($mesin)) {
            return [$ppic, $produksi, $gudang];
        }

        $normalizedTarget = $this->normalizeMesinValue($mesin);

        $ppic = array_values(array_filter($ppic, function ($row) use ($normalizedTarget) {
            return $this->isMesinMatch($row['nama_mesin'] ?? '', $normalizedTarget);
        }));

        $produksi = array_values(array_filter($produksi, function ($row) use ($normalizedTarget) {
            return $this->isMesinMatch($row['nama_mesin'] ?? '', $normalizedTarget);
        }));

        // Gudang tidak menyimpan nama_mesin, jadi dipetakan lewat no_spk yang terkait mesin terpilih.
        $spkList = array_unique(array_filter(array_merge(
            array_column($ppic, 'no_spk'),
            array_column($produksi, 'no_spk')
        )));

        if (!empty($spkList)) {
            $gudang = array_values(array_filter($gudang, function ($row) use ($spkList) {
                return in_array($row['no_spk'] ?? null, $spkList, true);
            }));
        } else {
            $gudang = [];
        }

        return [$ppic, $produksi, $gudang];
    }

    private function isMesinMatch(string $namaMesin, string $normalizedTarget): bool
    {
        if ($normalizedTarget === '') {
            return false;
        }

        $normalizedMesin = $this->normalizeMesinValue($namaMesin);
        return $normalizedMesin !== '' && str_contains($normalizedMesin, $normalizedTarget);
    }

    private function normalizeMesinValue(string $value): string
    {
        $normalized = preg_replace('/[^a-z0-9]/i', '', strtolower(trim($value)));
        return $normalized ?? '';
    }

    private function attachRevisiData(array $rows): array
    {
        if (empty($rows) || !$this->isRevisiTableReady()) {
            return $rows;
        }

        $ids = array_values(array_unique(array_filter(array_map(static function ($row) {
            return $row['id'] ?? null;
        }, $rows))));

        if (empty($ids)) {
            return $rows;
        }

        $revisiRows = $this->MRevisi
            ->whereIn('id_produksi', $ids)
            ->orderBy('id_produksi', 'ASC')
            ->orderBy('revisi_ke', 'ASC')
            ->orderBy('tanggal_revisi', 'ASC')
            ->findAll();

        $revisiMap = [];
        foreach ($revisiRows as $revisi) {
            $idPpic = (int) ($revisi['id_produksi'] ?? 0);
            $revisiKe = (int) ($revisi['revisi_ke'] ?? 0);
            $nilai = trim((string) ($revisi['nilai_revisi'] ?? ''));

            if ($idPpic <= 0 || $revisiKe < 1 || $revisiKe > 3 || $nilai === '') {
                continue;
            }

            $revisiMap[$idPpic][$revisiKe] = $revisi;
        }

        foreach ($rows as &$row) {
            $idPpic = (int) ($row['id'] ?? 0);
            $riwayat = $revisiMap[$idPpic] ?? [];

            if (empty($riwayat)) {
                $row['revisi_display'] = trim((string) ($row['revisi'] ?? ''));
                continue;
            }

            ksort($riwayat);
            $parts = [];
            $lastNilai = '';

            foreach ($riwayat as $ke => $detail) {
                $nilai = trim((string) ($detail['nilai_revisi'] ?? ''));
                if ($nilai === '') {
                    continue;
                }

                $lastNilai = $nilai;
                $tanggalRevisi = $detail['tanggal_revisi'] ?? '';
                $timeLabel = '';
                if (!empty($tanggalRevisi) && strtotime($tanggalRevisi) !== false) {
                    $timeLabel = ' (' . date('d/m H:i:s', strtotime($tanggalRevisi)) . ')';
                }

                $parts[] = 'R' . $ke . ': ' . $nilai . $timeLabel;
            }

            if ($lastNilai !== '') {
                $row['revisi'] = $lastNilai;
            }

            $row['revisi_display'] = implode(' | ', $parts);
        }
        unset($row);

        return $rows;
    }

    private function isRevisiTableReady(): bool
    {
        if ($this->hasRevisiTable !== null) {
            return $this->hasRevisiTable;
        }

        try {
            $this->hasRevisiTable = db_connect()->tableExists('revisi_produksi');
        } catch (\Throwable $e) {
            $this->hasRevisiTable = false;
        }

        return $this->hasRevisiTable;
    }


    public function exportExcel()
    {
        $data = $this->MProd->findAll();

        // Bersihkan output buffer (penting!)
        if (ob_get_length()) {
            ob_end_clean();
        }

        header("Content-Type: application/vnd.ms-excel; charset=utf-8");
        header("Content-Disposition: attachment; filename=data_produksi.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        echo "<tr>
                <th>No SPK</th>
                <th>Tanggal</th>
                <th>Nama Mesin</th>
                <th>Nama Produk</th>
                <th>Batch Number</th>
                <th>Shift</th>
                <th>Nomor Mesin</th>
                <th>Packing Isi</th>
                <th>Cycle Time</th>
                <th>Target</th>
                <th>Operator</th>
            </tr>";

        foreach ($data as $d) {
            echo "<tr>
                    <td>" . $d['no_spk'] . "</td>
                    <td>" . $d['tanggal'] . "</td>
                    <td>" . $d['nama_mesin'] . "</td>
                    <td>" . $d['nama_produk'] . "</td>
                    <td>" . $d['batch_number'] . "</td>
                    <td>" . $d['shift'] . "</td>
                    <td>" . $d['nomor_mesin'] . "</td>
                    <td>" . $d['packing_isi'] . "</td>
                    <td>" . $d['cycle_time'] . "</td>
                    <td>" . $d['target'] . "</td>
                    <td>" . $d['operator'] . "</td>
                </tr>";
        }

        echo "</table>";
        exit;
    }
}
