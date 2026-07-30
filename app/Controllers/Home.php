<?php

namespace App\Controllers;

use App\Models\MProduksiHeader;

class Home extends BaseController
{
    // Semua 9 tabel LOG MESIN beserta nama kolom produk utamanya
    private const MESIN_TABLES = [
        'mesin_becum'     => ['label' => 'Becum',      'produk_col' => 'produk_galon'],
        'mesin_ccm1'      => ['label' => 'CCM 1',      'produk_col' => 'produk_cap_galon'],
        'mesin_ccm2'      => ['label' => 'CCM 2',      'produk_col' => 'produk_cap_galon'],
        'mesin_powerjet1' => ['label' => 'Powerjet 1', 'produk_col' => 'produk_cap_galon'],
        'mesin_powerjet2' => ['label' => 'Powerjet 2', 'produk_col' => 'produk_cap_galon'],
        'mesin_ips1'      => ['label' => 'IPS 1',      'produk_col' => 'produk_preform'],
        'mesin_ips2'      => ['label' => 'IPS 2',      'produk_col' => 'produk_preform'],
        'mesin_ips3'      => ['label' => 'IPS 3',      'produk_col' => 'produk_preform'],
        'mesin_ips4'      => ['label' => 'IPS 4',      'produk_col' => 'produk_preform'],
    ];

    public function __construct()
    {
        helper(['form', 'url']);
    }

    public function index()
    {
        $q         = trim((string) $this->request->getGet('q'));
        $startDate = trim((string) ($this->request->getGet('start_date') ?? ''));
        $endDate   = trim((string) ($this->request->getGet('end_date')   ?? ''));
        $filter    = trim((string) ($this->request->getGet('filter')     ?? ''));

        // Shortcut filter periode
        if ($filter === 'day') {
            $startDate = $endDate = date('Y-m-d');
        } elseif ($filter === 'week') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate   = date('Y-m-d');
        } elseif ($filter === 'month') {
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate   = date('Y-m-d');
        }

        // ── LOG MESIN (dari oroppic) ──────────────────────────────────
        $mesinRows = $this->fetchLogMesin($q, $startDate, $endDate);
        
        // ── ATTACH PRODUKSI REALISASI to mesin rows ──────────────────
        $mesinRows = $this->attachProduksiRealisasiToMesin($mesinRows);

        // ── PRODUKSI ─────────────────────────────────────────────────
        $prodModel = new MProduksiHeader();
        $prodQ = $prodModel;
        if ($q !== '') {
            $prodQ = $prodQ->groupStart()
                ->like('nomor_spk', $q)
                ->orLike('nama_mesin', $q)
                ->orLike('nama_produk', $q)
                ->orLike('operator', $q)
                ->groupEnd();
        }
        if ($startDate !== '') $prodQ = $prodQ->where('tanggal >=', $startDate);
        if ($endDate   !== '') $prodQ = $prodQ->where('tanggal <=', $endDate);
        $prodData = $prodQ->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->findAll(500);

        // ── WAREHOUSE 2 ──────────────────────────────────────────────
        $warehouse2Data = $this->fetchWarehouse2Data($q, $startDate, $endDate);

        // ── MERGE ────────────────────────────────────────────────────
        $merged = $this->mergeRows($mesinRows, $prodData, $warehouse2Data);

        // ── PAGINASI ─────────────────────────────────────────────────
        $page    = max(1, (int) ($this->request->getGet('page') ?? 1));
        $perPage = 25;
        $total   = count($merged);
        $offset  = ($page - 1) * $perPage;
        $rows    = array_slice($merged, $offset, $perPage);

        $pager = service('pager')
            ->makeLinks($page, $perPage, $total, 'bootstrap_pagination', 0, 'home');

        return view('home', [
            'title'        => 'Dashboard Produksi',
            'rows'         => $rows,
            'prodData'     => $prodData,
            'q'            => $q,
            'start_date'   => $startDate,
            'end_date'     => $endDate,
            'filter'       => $filter,
            'pager'        => $pager,
            'total_rows'   => $total,
            'current_page' => $page,
            'per_page'     => $perPage,
            'modern_layout'=> true,
        ]);
    }

    // ----------------------------------------------------------------
    // Ambil dari semua 9 tabel mesin di oroppic
    // ----------------------------------------------------------------
    private function fetchLogMesin(string $q, string $startDate, string $endDate): array
    {
        $db   = $this->dbPpic();
        $rows = [];

        foreach (self::MESIN_TABLES as $table => $cfg) {
            if (!$db->tableExists($table)) continue;

            $builder = $db->table($table)
                ->where('deleted_at IS NULL')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(200);

            // Kolom produk utama mesin ini
            $produkCol = $cfg['produk_col'];

            if ($q !== '') {
                $builder->groupStart()
                    ->like('no_spk', $q)
                    ->orLike($produkCol, $q)
                    ->orLike('nomor_mesin', $q)
                    ->orLike('operator', $q)
                    ->groupEnd();
            }
            if ($startDate !== '') $builder->where('tanggal >=', $startDate);
            if ($endDate   !== '') $builder->where('tanggal <=', $endDate);

            $tableRows = $builder->get()->getResultArray();

            foreach ($tableRows as $r) {
                // Normalise ke format seragam
                $rows[] = [
                    'no_spk'      => trim((string) ($r['no_spk']       ?? '')),
                    'tanggal'     => trim((string) ($r['tanggal']       ?? '')),
                    'shif'        => trim((string) ($r['shif']          ?? '')),
                    'mesin_label' => $cfg['label'],
                    'mesin_tabel' => $table,
                    'nama_mesin'  => trim((string) ($r['nama_mesin']    ?? $cfg['label'])),
                    'nomor_mesin' => trim((string) ($r['nomor_mesin']   ?? '')),
                    'nama_produk' => trim((string) ($r[$produkCol]      ?? $r['nama_produk'] ?? '')),
                    'brand'       => trim((string) ($r['brand']         ?? '')),
                    'grade'       => trim((string) ($r['grade']         ?? '')),
                    'warna'       => trim((string) ($r['warna']         ?? '')),
                    'operator'    => trim((string) ($r['operator']      ?? '')),
                    // Target per shift
                    'target_shift1' => trim((string) ($r['target_shift1'] ?? '')),
                    'target_shift2' => trim((string) ($r['target_shift2'] ?? '')),
                    'target_shift3' => trim((string) ($r['target_shift3'] ?? '')),
                    // Realisasi per shift
                    'realisasi_shift1' => trim((string) ($r['realisasi_shift1'] ?? '')),
                    'realisasi_shift2' => trim((string) ($r['realisasi_shift2'] ?? '')),
                    'realisasi_shift3' => trim((string) ($r['realisasi_shift3'] ?? '')),
                    // Waktu realisasi
                    'waktu_awal_realisasi' => trim((string) ($r['waktu_awal_realisasi'] ?? '')),
                    'waktu_akhir_realisasi' => trim((string) ($r['waktu_akhir_realisasi'] ?? '')),
                    'keterangan'  => trim((string) ($r['keterangan']    ?? '')),
                    '_created_at' => $r['created_at'] ?? $r['tanggal'] ?? '',
                ];
            }
        }

        // Urutkan semua baris dari semua mesin: terbaru dulu
        usort($rows, fn($a, $b) => strcmp($b['_created_at'] ?? '', $a['_created_at'] ?? ''));

        return $rows;
    }

    // ----------------------------------------------------------------
    // Merge LOG MESIN + Produksi + Warehouse2 berdasarkan SPK|tanggal|shif
    // ----------------------------------------------------------------
    private function mergeRows(array $mesin, array $prod, array $warehouse2): array
    {
        $map = [];

        // LOG MESIN sebagai basis
        foreach ($mesin as $m) {
            $key = $m['no_spk'] . '|' . $m['tanggal'] . '|' . $m['shif'];
            $key .= '|' . $m['mesin_tabel'];
            $map[$key] = ['mesin' => $m, 'produksi' => null, 'warehouse2' => null, '_sort' => $m['_created_at']];
        }

        // Produksi — cari match berdasarkan SPK + tanggal + shift
        foreach ($prod as $pr) {
            $spk  = trim((string) ($pr['nomor_spk'] ?? ''));
            $tgl  = trim((string) ($pr['tanggal']   ?? ''));
            $shif = trim((string) ($pr['shift']     ?? ''));

            $matched = false;
            foreach ($map as $k => &$row) {
                if (
                    $row['mesin'] !== null &&
                    $row['mesin']['no_spk']  === $spk &&
                    $row['mesin']['tanggal'] === $tgl &&
                    $row['mesin']['shif']    === $shif &&
                    $row['produksi'] === null
                ) {
                    $row['produksi'] = $pr;
                    $matched = true;
                    break;
                }
            }
            unset($row);

            if (!$matched) {
                $key = $spk . '|' . $tgl . '|' . $shif . '|_prod';
                if (!isset($map[$key])) {
                    $map[$key] = ['mesin' => null, 'produksi' => $pr, 'warehouse2' => null, '_sort' => $tgl];
                }
            }
        }

        // Warehouse2 — cari match SPK + tanggal
        foreach ($warehouse2 as $w) {
            $spk = trim((string) ($w['spk']     ?? ''));
            $tgl = trim((string) ($w['tanggal'] ?? ''));

            $matched = false;
            foreach ($map as $k => &$row) {
                $mSpk = $row['mesin']['no_spk']      ?? $row['produksi']['nomor_spk'] ?? '';
                $mTgl = $row['mesin']['tanggal']      ?? $row['produksi']['tanggal']   ?? '';
                if ($mSpk === $spk && $mTgl === $tgl && $row['warehouse2'] === null) {
                    $row['warehouse2'] = $w;
                    $matched = true;
                    break;
                }
            }
            unset($row);

            if (!$matched) {
                $key = $spk . '|' . $tgl . '|_w2';
                if (!isset($map[$key])) {
                    $map[$key] = ['mesin' => null, 'produksi' => null, 'warehouse2' => $w, '_sort' => $tgl];
                }
            }
        }

        // Urutkan terbaru dulu
        uasort($map, fn($a, $b) => strcmp($b['_sort'] ?? '', $a['_sort'] ?? ''));

        return array_values($map);
    }

    // ----------------------------------------------------------------
    // Warehouse2 dari tabel orowarehouse
    // ----------------------------------------------------------------
    private function fetchWarehouse2Data(string $q, string $startDate, string $endDate): array
    {
        try {
            $db = \Config\Database::connect('warehouse');
        } catch (\Throwable $e) {
            return [];
        }

        if (!$db->tableExists('orowarehouse')) {
            return [];
        }

        $builder = $db->table('orowarehouse')
            ->where('deleted_at IS NULL', null, false)
            ->orderBy('tanggal', 'DESC')
            ->orderBy('id', 'DESC')
            ->limit(500);

        if ($q !== '') {
            $builder->groupStart()
                ->like('spk', $q)
                ->orLike('produk', $q)
                ->orLike('operator', $q)
                ->orLike('material', $q)
                ->groupEnd();
        }
        if ($startDate !== '') $builder->where('DATE(tanggal) >=', $startDate);
        if ($endDate   !== '') $builder->where('DATE(tanggal) <=', $endDate);

        $rows = $builder->get()->getResultArray();

        $result = [];
        foreach ($rows as $r) {
            $result[] = [
                'spk'          => trim((string) ($r['spk']          ?? '')),
                'tanggal'      => substr(trim((string) ($r['tanggal'] ?? '')), 0, 10),
                'produk'       => trim((string) ($r['produk']        ?? '')),
                'shift'        => trim((string) ($r['shift']         ?? '')),
                'hasil_bagus'  => $r['hasil_bagus']  ?? '',
                'hasil_reject' => $r['hasil_reject'] ?? '',
                'material'     => trim((string) ($r['material']      ?? '')),
                'colorant'     => trim((string) ($r['colorant']      ?? '')),
                'packaging'    => trim((string) ($r['packaging']     ?? '')),
                'operator'     => trim((string) ($r['operator']      ?? '')),
                'status'       => trim((string) ($r['status']        ?? '')),
            ];
        }

        return $result;
    }

    // ----------------------------------------------------------------
    // Attach per-shift realisasi from produksi_headers to mesin rows
    // ----------------------------------------------------------------
    private function attachProduksiRealisasiToMesin(array $rows): array
    {
        // Pre-initialise all shift keys on every row
        foreach ($rows as &$row) {
            $row['produksi_realisasi_shift1'] = null;
            $row['produksi_realisasi_shift2'] = null;
            $row['produksi_realisasi_shift3'] = null;
            $row['produksi_bagus_shift1']     = null;
            $row['produksi_bagus_shift2']     = null;
            $row['produksi_bagus_shift3']     = null;
            $row['produksi_reject_shift1']    = null;
            $row['produksi_reject_shift2']    = null;
            $row['produksi_reject_shift3']    = null;
        }
        unset($row);

        // Collect all tanggal values from mesin rows
        $tanggalSet = [];
        foreach ($rows as $row) {
            $tgl = trim((string) ($row['tanggal'] ?? ''));
            if ($tgl !== '') $tanggalSet[$tgl] = true;
        }

        if (empty($tanggalSet)) {
            return $rows;
        }

        try {
            $db       = $this->dbProduksi();
            $tanggals = array_values(array_keys($tanggalSet));

            // Query semua produksi_headers untuk tanggal yang ada di mesin rows
            // Simpel — tidak filter per SPK, cukup per tanggal
            $tPH = implode(',', array_fill(0, count($tanggals), '?'));
            $sql = "
                SELECT
                    nomor_spk,
                    tanggal,
                    shift,
                    SUM(grand_total_bagus)  AS total_bagus,
                    SUM(grand_total_reject) AS total_reject
                FROM produksi_headers
                WHERE deleted_at IS NULL
                  AND tanggal IN ($tPH)
                GROUP BY nomor_spk, tanggal, shift
            ";

            $prodRows = $db->query($sql, $tanggals)->getResultArray();

            // Build lookup: normSpk|tanggal|shift => [bagus, reject]
            $bySpk = [];
            foreach ($prodRows as $r) {
                $normSpk = (string)(int)trim((string) $r['nomor_spk']);
                $key     = $normSpk . '|' . $r['tanggal'] . '|' . $r['shift'];
                if (!isset($bySpk[$key])) {
                    $bySpk[$key] = ['bagus' => 0, 'reject' => 0];
                }
                $bySpk[$key]['bagus']  += (int) $r['total_bagus'];
                $bySpk[$key]['reject'] += (int) $r['total_reject'];
            }

            // Attach to each mesin row
            foreach ($rows as &$row) {
                $normSpk = (string)(int)trim((string) ($row['no_spk'] ?? ''));
                $tanggal = trim((string) ($row['tanggal'] ?? ''));

                for ($shift = 1; $shift <= 3; $shift++) {
                    $key = "$normSpk|$tanggal|$shift";
                    if (isset($bySpk[$key])) {
                        $row["produksi_realisasi_shift$shift"] = $bySpk[$key]['bagus'];
                        $row["produksi_bagus_shift$shift"]     = $bySpk[$key]['bagus'];
                        $row["produksi_reject_shift$shift"]    = $bySpk[$key]['reject'];
                    }
                }
            }
            unset($row);

        } catch (\Throwable $e) {
            log_message('error', 'attachProduksiRealisasiToMesin failed: ' . $e->getMessage());
        }

        return $rows;
    }
}

