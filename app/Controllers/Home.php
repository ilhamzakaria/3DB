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

        // Extract unique values for column filters from the FULL dataset (before view-level pagination if any)
        $uniqueValues = $this->extractUniqueValuesForFilters($ppic, $produksi, $gudang);

        return view('home', [
            'title' => 'Data PPIC, Produksi & Gudang',
            'ppic' => $ppic,
            'produksi' => $produksi,
            'gudang' => $gudang,
            'q' => $filters['q'],
            'periode' => $filters['periode'],
            'tanggal' => $filters['tanggal'],
            'start_date' => $filters['start_date'],
            'end_date' => $filters['end_date'],
            'col_filter' => $filters['col_filter'],
            'unique_values' => $uniqueValues,
        ]);
    }

    private function collectHomeFilters(): array
    {
        $q = trim((string) $this->request->getGet('q'));
        $periode = trim((string) $this->request->getGet('periode'));
        $tanggal = trim((string) $this->request->getGet('tanggal'));
        $startDate = trim((string) $this->request->getGet('start_date'));
        $endDate = trim((string) $this->request->getGet('end_date'));
        $colFilter = $this->request->getGet('col_filter') ?? [];

        if ($startDate !== '' && $endDate !== '' && $endDate < $startDate) {
            [$startDate, $endDate] = [$endDate, $startDate];
        }

        return [
            'q' => $q,
            'periode' => $periode,
            'tanggal' => $tanggal,
            'start_date' => $startDate,
            'end_date' => $endDate,
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

        // Fetch all data
        $ppicData = $builderPpic->orderBy('tanggal', 'DESC')->orderBy('shif', 'DESC')->findAll();
        $prodData = $builderProd->orderBy('tanggal', 'DESC')->orderBy('shift', 'DESC')->findAll();
        
        // Fetch Gudang details from individual module tables
        $gudangData = $this->fetchGudangFromModules($filters);

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

            if ($idPpic <= 0 || $revisiKe < 1 || $revisiKe > 3 || $nilai === '') {
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
    private function fetchGudangFromModules(array $filters): array
    {
        $db = db_connect();
        $gudangMap = [];

        $addData = function($table, $fieldToUpdate, $filters, $conditionField = null, $conditionValue = null, $dateField = 'created_at') use ($db, &$gudangMap) {
            if (!$this->tableExists($table)) return;

            $builder = $db->table($table);
            
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
            return db_connect()->tableExists($table);
        } catch (\Throwable $e) {
            return false;
        }
    }
}

