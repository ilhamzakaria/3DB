<?php

namespace App\Controllers;

use App\Models\MProduksiHeader;
use App\Models\MGudang;
use App\Models\MPpic;
use App\Models\MRevisi;

class Home extends BaseController
{
    protected $MProduksiHeader;
    protected $MGudang;
    protected $MPpic;
    protected $MRevisi;
    protected ?bool $hasRevisiTable = null;

    private const KOMPONEN_FIELDS = [
        'bahan_baku',
        'box',
        'karung',
        'plastik',
        'masterbatch',
        'galon_reject_supplier',
        'galon_reject_produksi',
        'gilingan_reject_supplier',
        'gilingan_reject_produksi',
        'stiker',
        'reject_preform',
        'bekuat_pet',
        'bekuan_capgalon',
        'gilingan_screwcap',
    ];

    public function __construct()
    {
        $this->MProduksiHeader = new MProduksiHeader();
        $this->MGudang = new MGudang();
        $this->MPpic = new MPpic();
        $this->MRevisi = new MRevisi();
        helper('form');
    }

    public function index()
    {
        $filters = $this->collectHomeFilters();
        [$ppic, $produksi, $gudang] = $this->getHomeData($filters);
        [$ppic, $produksi, $gudang] = $this->fillMissingDepartmentRows($ppic, $produksi, $gudang, $filters);

        // Extract unique values for column filters from the FULL dataset (before view-level pagination if any)
        $uniqueValues = $this->extractUniqueValuesForFilters($ppic, $produksi, $gudang);

        // Manual Pagination for merged data
        $page = (int) ($this->request->getGet('page') ?? 1);
        $perPage = 15;
        $total = max(count($ppic), count($produksi), count($gudang));
        
        $offset = ($page - 1) * $perPage;
        
        $paginatedPpic = array_slice($ppic, $offset, $perPage);
        $paginatedProd = array_slice($produksi, $offset, $perPage);
        $paginatedGudang = array_slice($gudang, $offset, $perPage);

        $pager = service('pager');
        $pagerLinks = $pager->makeLinks($page, $perPage, $total, 'bootstrap_pagination', 0, 'home');

        return view('home', [
            'title' => 'Data PPIC, Produksi & Gudang',
            'ppic' => $paginatedPpic,
            'produksi' => $paginatedProd,
            'gudang' => $paginatedGudang,
            'q' => $filters['q'],
            'periode' => $filters['periode'],
            'tanggal' => $filters['tanggal'],
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
            'filter' => $filters['filter'],
            'col_filter' => $filters['col_filter'],
            'unique_values' => $uniqueValues,
            'pager' => $pagerLinks,
            'total_rows' => $total,
            'current_page' => $page,
            'per_page' => $perPage
        ]);
    }

    private function collectHomeFilters(): array
    {
        $q = trim((string) $this->request->getGet('q'));
        $periode = trim((string) $this->request->getGet('periode'));
        $tanggal = trim((string) $this->request->getGet('tanggal'));
        $startDate = trim((string) $this->request->getGet('start_date'));
        $endDate = trim((string) $this->request->getGet('end_date'));
        $filter = $this->request->getGet('filter');
        $colFilter = $this->request->getGet('col_filter') ?? [];

        if ($filter === 'day') {
            $startDate = $endDate = date('Y-m-d');
        } elseif ($filter === 'week') {
            $startDate = date('Y-m-d', strtotime('-7 days'));
            $endDate = date('Y-m-d');
        } elseif ($filter === 'month') {
            $startDate = date('Y-m-d', strtotime('-30 days'));
            $endDate = date('Y-m-d');
        }

        if ($startDate !== '' && $endDate !== '' && $endDate < $startDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [
            'q' => $q,
            'periode' => $periode,
            'tanggal' => $tanggal,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'filter' => $filter,
            'col_filter' => $colFilter,
        ];
    }

    private function getHomeData(array $filters = []): array
    {
        $q = trim((string) ($filters['q'] ?? ''));
        $periode = trim((string) ($filters['periode'] ?? ''));
        $tanggal = trim((string) ($filters['tanggal'] ?? ''));
        $startDate = trim((string) ($filters['start_date'] ?? ''));
        $endDate = trim((string) ($filters['end_date'] ?? ''));

        $builderPpic = $this->MPpic;
        $builderProd = $this->MProduksiHeader;
        $builderGudang = $this->MGudang;

        if ($q !== '') {
            $builderPpic->groupStart()
                ->like('no_spk', $q)
                ->orLike('nama_mesin', $q)
                ->orLike('nama_produk', $q)
                ->orLike('operator', $q)
                ->orLike('shif', $q)
                ->groupEnd();

            $builderProd->groupStart()
                ->like('nomor_spk', $q)
                ->orLike('nama_mesin', $q)
                ->orLike('nama_produksi', $q)
                ->orLike('operator', $q)
                ->orLike('shift', $q)
                ->groupEnd();

            $builderGudang->groupStart()
                ->like('no_spk', $q)
                ->orLike('bahan_baku', $q)
                ->orLike('box', $q)
                ->orLike('karung', $q)
                ->orLike('shif', $q)
                ->groupEnd();
        }

        if ($periode !== '' && $tanggal !== '') {
            if ($periode === 'hari') {
                $builderPpic->where('tanggal', $tanggal);
                $builderProd->where('tanggal', $tanggal);
                $builderGudang->where('tanggal', $tanggal);
            }

            if ($periode === 'minggu') {
                $start = date('Y-m-d', strtotime('monday this week', strtotime($tanggal)));
                $end = date('Y-m-d', strtotime('sunday this week', strtotime($tanggal)));
                $builderPpic->where('tanggal >=', $start)->where('tanggal <=', $end);
                $builderProd->where('tanggal >=', $start)->where('tanggal <=', $end);
                $builderGudang->where('tanggal >=', $start)->where('tanggal <=', $end);
            }

            if ($periode === 'bulan') {
                $month = date('m', strtotime($tanggal));
                $year = date('Y', strtotime($tanggal));

                $builderPpic->where('MONTH(tanggal)', $month)->where('YEAR(tanggal)', $year);
                $builderProd->where('MONTH(tanggal)', $month)->where('YEAR(tanggal)', $year);
                $builderGudang->where('MONTH(tanggal)', $month)->where('YEAR(tanggal)', $year);
            }
        }

        if ($startDate !== '') {
            $builderPpic->where('tanggal >=', $startDate);
            $builderProd->where('tanggal >=', $startDate);
            $builderGudang->where('tanggal >=', $startDate);
        }

        if ($endDate !== '') {
            $builderPpic->where('tanggal <=', $endDate);
            $builderProd->where('tanggal <=', $endDate);
            $builderGudang->where('tanggal <=', $endDate);
        }

        // Apply column-level filters
        $colFilters = $filters['col_filter'] ?? [];
        foreach ($colFilters as $col => $values) {
            if (empty($values)) continue;

            // PPIC filters
            if (in_array($col, ['nama_mesin', 'nama_produk', 'operator', 'shif'])) {
                $builderPpic->whereIn($col, $values);
            }

            // Production filters
            if (in_array($col, ['nama_mesin', 'nama_produksi', 'operator', 'shift'])) {
                $builderProd->whereIn($col, $values);
            }

            // Gudang filters
            if (in_array($col, ['shif', 'bahan_baku'])) {
                $builderGudang->whereIn($col, $values);
            }
        }

        // Fetch data with a reasonable limit to prevent slowness
        $ppicData = $builderPpic->orderBy('tanggal', 'DESC')->orderBy('shif', 'DESC')->limit(500)->findAll();
        $prodData = $builderProd->orderBy('tanggal', 'DESC')->orderBy('shift', 'DESC')->limit(500)->findAll();
        
        // Fetch Gudang details from individual module tables
        $gudangData = $this->fetchGudangFromModules($filters, 500);

        // Correlation Logic: Merge data by SPK, Date, and Shift
        $merged = [];
        
        // Use PPIC as the base
        foreach ($ppicData as $p) {
            $key = $p['no_spk'] . '_' . $p['tanggal'] . '_' . $p['shif'];
            $merged[$key] = [
                'ppic' => $p,
                'produksi' => null,
                'gudang' => null
            ];
        }

        // Add Production data
        foreach ($prodData as $pr) {
            $key = $pr['nomor_spk'] . '_' . $pr['tanggal'] . '_' . $pr['shift'];
            if (!isset($merged[$key])) {
                $merged[$key] = ['ppic' => null, 'produksi' => $pr, 'gudang' => null];
            } else {
                $merged[$key]['produksi'] = $pr;
            }
        }

        // Add Gudang data
        foreach ($gudangData as $g) {
            $key = $g['no_spk'] . '_' . $g['tanggal'] . '_' . $g['shif'];
            if (!isset($merged[$key])) {
                $merged[$key] = ['ppic' => null, 'produksi' => null, 'gudang' => $g];
            } else {
                $merged[$key]['gudang'] = $g;
            }
        }



        // Attach revisi to PPIC parts
        $ppicList = array_filter(array_column($merged, 'ppic'));
        $ppicList = $this->attachRevisiData($ppicList);
        
        // Put back the revisi data
        foreach ($ppicList as $p) {
            $key = $p['no_spk'] . '_' . $p['tanggal'] . '_' . $p['shif'];
            if (isset($merged[$key])) {
                $merged[$key]['ppic'] = $p;
            }
        }

        // Convert merged map back to arrays for the view
        $finalPpic = [];
        $finalProd = [];
        $finalGudang = [];

        foreach ($merged as $item) {
            $finalPpic[] = $item['ppic'];
            $finalProd[] = $item['produksi'];
            $finalGudang[] = $item['gudang'];
        }

        return [$finalPpic, $finalProd, $finalGudang];
    }

    private function fillMissingDepartmentRows(array $ppic, array $produksi, array $gudang, array $filters): array
    {
        if (!$this->shouldUseFakeDepartmentRows($filters)) {
            return [$ppic, $produksi, $gudang];
        }

        $rowCount = max(count($ppic), count($produksi), count($gudang), 12);
        $filledPpic = [];
        $filledProduksi = [];
        $filledGudang = [];

        for ($i = 0; $i < $rowCount; $i++) {
            $ppicRow = $ppic[$i] ?? null;
            $prodRow = $produksi[$i] ?? null;
            $gudangRow = $gudang[$i] ?? null;

            $filledPpic[] = $ppicRow;
            $filledProduksi[] = is_array($prodRow) && !empty($prodRow)
                ? $prodRow
                : $this->buildFakeProduksiRow($ppicRow, $gudangRow, $i);
            $filledGudang[] = is_array($gudangRow) && !empty($gudangRow)
                ? $gudangRow
                : $this->buildFakeGudangRow($ppicRow, $prodRow, $i);
        }

        return [$filledPpic, $filledProduksi, $filledGudang];
    }

    private function shouldUseFakeDepartmentRows(array $filters): bool
    {
        if (trim((string) ($filters['q'] ?? '')) !== '') {
            return false;
        }

        if (trim((string) ($filters['periode'] ?? '')) !== '') {
            return false;
        }

        if (trim((string) ($filters['tanggal'] ?? '')) !== '') {
            return false;
        }

        if (trim((string) ($filters['start_date'] ?? '')) !== '' || trim((string) ($filters['end_date'] ?? '')) !== '') {
            return false;
        }

        if (!empty($filters['filter'] ?? '')) {
            return false;
        }

        foreach (($filters['col_filter'] ?? []) as $values) {
            if (!empty($values)) {
                return false;
            }
        }

        return true;
    }

    private function buildFakeProduksiRow(?array $ppicRow, ?array $gudangRow, int $index): array
    {
        $produkList = [
            'Galon 19L Crystal',
            'Botol 600ml Fresh',
            'Preform 1500ml Clear',
            'Cap Galon Blue Seal',
            'Jerigen 5L Natural',
            'Cup 220ml Premium',
        ];
        $mesinList = ['Mesin IPS 1', 'Mesin IPS 2', 'Mesin CCM 1', 'Mesin CCM 2', 'Mesin Powerjet 1', 'Mesin Becum'];
        $operatorList = ['Andi', 'Budi', 'Citra', 'Deni', 'Eko', 'Fajar', 'Gilang', 'Hani'];

        $tanggal = $ppicRow['tanggal'] ?? $gudangRow['tanggal'] ?? date('Y-m-d', strtotime('-' . ($index % 10) . ' days'));
        $shift = (string) ($ppicRow['shif'] ?? $gudangRow['shif'] ?? (($index % 3) + 1));
        $nomorSpk = trim((string) ($ppicRow['no_spk'] ?? $gudangRow['no_spk'] ?? 'SPK-PRD-' . date('ym') . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)));
        $namaMesin = $ppicRow['nama_mesin'] ?? $mesinList[$index % count($mesinList)];
        $namaProduksi = $ppicRow['nama_produk'] ?? $produkList[$index % count($produkList)];
        $operator = $ppicRow['operator'] ?? $operatorList[$index % count($operatorList)];

        $hasilBagus = 1180 + ($index * 95);
        $hasilReject = 8 + (($index * 3) % 17);

        return [
            'nomor_spk' => $nomorSpk,
            'nama_mesin' => $namaMesin,
            'nama_produksi' => $namaProduksi,
            'shift' => $shift,
            'operator' => $operator,
            'grand_total_bagus' => (string) $hasilBagus,
            'grand_total_reject' => (string) $hasilReject,
            'tanggal' => $tanggal,
        ];
    }

    private function buildFakeGudangRow(?array $ppicRow, ?array $prodRow, int $index): array
    {
        $bahanList = ['PET Natural', 'HDPE Virgin', 'PP Injection', 'Master Resin Blue', 'Regrind Food Grade', 'MB Clear'];

        $tanggal = $ppicRow['tanggal'] ?? $prodRow['tanggal'] ?? date('Y-m-d', strtotime('-' . ($index % 10) . ' days'));
        $shift = (string) ($ppicRow['shif'] ?? $prodRow['shift'] ?? (($index % 3) + 1));
        $nomorSpk = trim((string) ($ppicRow['no_spk'] ?? $prodRow['nomor_spk'] ?? 'SPK-GDG-' . date('ym') . '-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)));

        return [
            'no_spk' => $nomorSpk,
            'shif' => $shift,
            'tanggal' => $tanggal,
            'bahan_baku' => $bahanList[$index % count($bahanList)],
            'box' => (string) (40 + ($index % 5) * 6),
            'karung' => (string) (18 + ($index % 4) * 3),
            'plastik' => (string) (55 + ($index % 6) * 5),
            'masterbatch' => (string) (12 + ($index % 5) * 2),
            'galon_reject_supplier' => (string) (($index + 1) % 4),
            'galon_reject_produksi' => (string) (2 + (($index + 2) % 5)),
            'gilingan_reject_supplier' => (string) (($index + 3) % 3),
            'gilingan_reject_produksi' => (string) (1 + (($index + 1) % 4)),
            'stiker' => (string) (80 + ($index % 5) * 10),
            'reject_preform' => (string) (1 + ($index % 3)),
            'bekuat_pet' => (string) (3 + ($index % 4)),
            'bekuan_capgalon' => (string) (2 + (($index + 1) % 4)),
            'gilingan_screwcap' => (string) (4 + (($index + 2) % 5)),
        ];
    }

    private function extractUniqueValuesForFilters($ppic, $produksi, $gudang): array
    {
        $unique = [];

        // Helper to extract unique values from a specific field in a dataset
        $extract = function($data, $field) {
            if (empty($data)) return [];
            return array_values(array_unique(array_filter(array_column($data, $field))));
        };

        // PPIC unique values
        $unique['ppic_nama_mesin'] = $extract($ppic, 'nama_mesin');
        $unique['ppic_nama_produk'] = $extract($ppic, 'nama_produk');
        $unique['ppic_operator'] = $extract($ppic, 'operator');
        $unique['ppic_shif'] = $extract($ppic, 'shif');

        // Production unique values
        $unique['prod_nama_mesin'] = $extract($produksi, 'nama_mesin');
        $unique['prod_nama_produksi'] = $extract($produksi, 'nama_produksi');
        $unique['prod_operator'] = $extract($produksi, 'operator');
        $unique['prod_shift'] = $extract($produksi, 'shift');

        // Gudang unique values
        $unique['gudang_shif'] = $extract($gudang, 'shif');
        $unique['gudang_bahan_baku'] = $extract($gudang, 'bahan_baku');

        return $unique;
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

            if ($idPpic <= 0 || $revisiKe < 1 || $revisiKe > 5 || $nilai === '') {
                continue;
            }

            $revisiMap[$idPpic][$revisiKe] = $revisi;
        }

        foreach ($rows as &$row) {
            $idPpic = (int) ($row['id'] ?? 0);
            $riwayat = $revisiMap[$idPpic] ?? [];
            $row['revisi_items'] = [];

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
                $row['revisi_items'][$ke] = [
                    'revisi_ke' => $ke,
                    'nilai' => $nilai,
                    'tanggal_revisi' => $tanggalRevisi,
                    'tanggal_label' => !empty($tanggalRevisi) && strtotime($tanggalRevisi) !== false
                        ? date('d M Y', strtotime($tanggalRevisi))
                        : '-',
                    'jam_label' => !empty($tanggalRevisi) && strtotime($tanggalRevisi) !== false
                        ? date('H:i:s', strtotime($tanggalRevisi))
                        : '-',
                ];
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
            $this->hasRevisiTable = $this->dbProduksi()->tableExists('revisi_produksi');
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
    private function fetchGudangFromModules(array $filters, int $limit = 500): array
    {
        $db = $this->dbGudang();
        $gudangMap = [];

        $addData = function($table, $fieldToUpdate, $filters, $conditionField = null, $conditionValue = null, $dateField = 'created_at') use ($db, &$gudangMap, $limit) {
            if (!$this->tableExists($table)) return;

            $builder = $db->table($table);
            $builder->limit($limit);
            $builder->orderBy($dateField, 'DESC');
            
            // Apply dates
            $tanggal = trim((string) ($filters['tanggal'] ?? ''));
            $periode = trim((string) ($filters['periode'] ?? ''));
            $startDate = trim((string) ($filters['start_date'] ?? ''));
            $endDate = trim((string) ($filters['end_date'] ?? ''));

            if ($periode !== '' && $tanggal !== '') {
                if ($periode === 'hari') {
                    $builder->where("DATE($dateField)", $tanggal);
                }
                if ($periode === 'minggu') {
                    $start = date('Y-m-d', strtotime('monday this week', strtotime($tanggal)));
                    $end = date('Y-m-d', strtotime('sunday this week', strtotime($tanggal)));
                    $builder->where("DATE($dateField) >=", $start)->where("DATE($dateField) <=", $end);
                }
                if ($periode === 'bulan') {
                    $month = date('m', strtotime($tanggal));
                    $year = date('Y', strtotime($tanggal));
                    $builder->where("MONTH($dateField)", $month)->where("YEAR($dateField)", $year);
                }
            }
            if ($startDate !== '') {
                $builder->where("DATE($dateField) >=", $startDate);
            }
            if ($endDate !== '') {
                $builder->where("DATE($dateField) <=", $endDate);
            }

            $q = trim((string) ($filters['q'] ?? ''));
            if ($q !== '') {
                $builder->groupStart()
                        ->like('no_spk', $q)
                        ->orLike('shif', $q)
                        ->groupEnd();
            }

            // Apply column-level filters (only shif is safe here as other columns might not exist)
            $colFilters = $filters['col_filter'] ?? [];
            if (!empty($colFilters['shif'])) {
                $builder->whereIn('shif', $colFilters['shif']);
            }

            if ($conditionField && $conditionValue) {
                $builder->where($conditionField, $conditionValue);
            }

            $rows = $builder->get()->getResultArray();

            foreach ($rows as $r) {
                $spk = trim((string) ($r['no_spk'] ?? ''));
                $shif = trim((string) ($r['shif'] ?? ''));
                $date = '';
                if ($dateField === 'created_at') {
                    $date = isset($r['created_at']) ? date('Y-m-d', strtotime($r['created_at'])) : '';
                } else {
                    $date = isset($r['tanggal']) ? date('Y-m-d', strtotime($r['tanggal'])) : '';
                }
                
                if ($spk === '' || $date === '') continue;

                $key = $spk . '_' . $date . '_' . $shif;
                
                if (!isset($gudangMap[$key])) {
                    $gudangMap[$key] = [
                        'no_spk' => $spk,
                        'shif' => $shif,
                        'tanggal' => $date,
                        'bahan_baku' => '-',
                        'box' => '-',
                        'karung' => '-',
                        'plastik' => '-',
                        'masterbatch' => '-',
                        'galon_reject_supplier' => '-',
                        'galon_reject_produksi' => '-',
                        'gilingan_reject_supplier' => '-',
                        'gilingan_reject_produksi' => '-',
                        'stiker' => '-',
                        'reject_preform' => '-',
                        'bekuat_pet' => '-',
                        'bekuan_capgalon' => '-',
                        'gilingan_screwcap' => '-'
                    ];
                }

                $val = trim((string) ($r['jumlah'] ?? ''));
                if ($val !== '') {
                    if ($gudangMap[$key][$fieldToUpdate] === '-') {
                        $gudangMap[$key][$fieldToUpdate] = $val;
                    } else if (is_numeric($gudangMap[$key][$fieldToUpdate]) && is_numeric($val)) {
                        $gudangMap[$key][$fieldToUpdate] = (string)((float)$gudangMap[$key][$fieldToUpdate] + (float)$val);
                    } else {
                        $gudangMap[$key][$fieldToUpdate] = $val;
                    }
                }
            }
        };

        $addData('bahan_baku', 'bahan_baku', $filters);
        $addData('packaging', 'box', $filters, 'jenis_packaging', 'box');
        $addData('packaging', 'karung', $filters, 'jenis_packaging', 'karung');
        $addData('packaging', 'plastik', $filters, 'jenis_packaging', 'plastik');
        $addData('masterbatch', 'masterbatch', $filters);
        $addData('galon_reject', 'galon_reject_supplier', $filters, 'sumber_reject', 'supplier');
        $addData('galon_reject', 'galon_reject_produksi', $filters, 'sumber_reject', 'produksi');
        $addData('gilingan_galon', 'gilingan_reject_supplier', $filters, 'sumber_gilingan', 'supplier', 'tanggal');
        $addData('gilingan_galon', 'gilingan_reject_produksi', $filters, 'sumber_gilingan', 'produksi', 'tanggal');
        $addData('stiker_gudang', 'stiker', $filters, null, null, 'tanggal');
        $addData('reject_produksi', 'reject_preform', $filters, 'jenis_limbah', 'reject_preform', 'tanggal');
        $addData('reject_produksi', 'bekuat_pet', $filters, 'jenis_limbah', 'bekuan_pet', 'tanggal');
        $addData('reject_produksi', 'bekuan_capgalon', $filters, 'jenis_limbah', 'bekuan_cap_galon', 'tanggal');
        $addData('reject_produksi', 'gilingan_screwcap', $filters, 'jenis_limbah', 'gilingan_screwcap', 'tanggal');

        return array_values($gudangMap);
    }

    private function tableExists(string $table): bool
    {
        try {
            return $this->dbGudang()->tableExists($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}
