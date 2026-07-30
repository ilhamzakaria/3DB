<?php

namespace App\Controllers;

use App\Controllers\Traits\MesinModuleTrait;
use App\Controllers\Traits\MesinBrandManagementTrait;
use App\Models\MMesinIps3;
use App\Models\MPpicMesinIps3;

class MesinIps3 extends BaseController
{
    use MesinModuleTrait, MesinBrandManagementTrait;

    private const VIEW_PATH = 'gudang/mesinips3';

    protected $MMesin;
    protected $MMesinPpic;

    protected function mesinSlug(): string { return 'mesin_ips3'; }

    public function __construct()
    {
        $this->MMesin     = new MMesinIps3();
        $this->MMesinPpic = new MPpicMesinIps3();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q         = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');
        $query     = $this->activeMesin();
        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk_preform', $q)->orLike('brand', $q)
                ->orLike('warna', $q)->orLike('grade', $q)
                ->orLike('no_spk', $q)->orLike('shif', $q)
                ->groupEnd();
        }
        if (!empty($startDate)) $query = $query->where('tanggal >=', $startDate);
        if (!empty($endDate))   $query = $query->where('tanggal <=', $endDate);
        $data_list = $query->orderBy('tanggal','DESC')->orderBy('created_at','DESC')->paginate(15, $this->mesinSlug());
        
        // When in PPIC context, attach realisasi data from produksi_headers
        if ($this->isPpicContext() && !empty($data_list)) {
            $data_list = $this->attachProduksiRealisasi($data_list);
        }
        
        return view(self::VIEW_PATH, array_merge([
            'title'           => 'Mesin IPS 3',
            'data_list'       => $data_list,
            'pager'           => $this->activeMesin()->pager,
            'q'               => $q,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'current_machine' => $this->mesinSlug(),
        ], $this->mesinViewContext()));
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) return redirect()->to($this->indexRoute());
        if ($this->activeMesin()->insert($this->getIpsFields())) {
            session()->setFlashdata('success', 'Data Mesin IPS 3 berhasil ditambahkan');
        } else { session()->setFlashdata('error', 'Gagal menambahkan data'); }
        return redirect()->to($this->indexRoute());
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) return redirect()->to($this->indexRoute());
        $id = $id ?: $this->request->getPost('id');
        if ($this->activeMesin()->update($id, $this->getIpsFields())) {
            session()->setFlashdata('success', 'Data Mesin IPS 3 berhasil diupdate');
        } else { session()->setFlashdata('error', 'Gagal mengupdate data'); }
        return redirect()->to($this->indexRoute());
    }

    private function getIpsFields(): array
    {
        $p = fn(string $k) => trim((string) $this->request->getPost($k));
        return [
            'tanggal'               => $p('tanggal') ?: date('Y-m-d'),
            'no_spk'                => $p('no_spk') !== '' ? (int) $p('no_spk') : null,
            'nomor_mesin'           => $p('nomor_mesin'),
            'shif'                  => $p('shif'),
            'status'                => $p('status'),
            'jam_awal'              => $p('jam_awal'),
            'jam_akhir'             => $p('jam_akhir'),
            'produk_preform'        => $p('produk_preform'),
            'brand'                 => $p('brand'),
            'grade'                 => $p('grade'),
            'warna'                 => $p('warna'),
            'berat'                 => $p('berat'),
            'jam'                   => $p('jam'),
            'waktu_akhir_target'    => $p('waktu_akhir_target'),
            'target_shift1'         => $p('target_shift1'),
            'target_shift2'         => $p('target_shift2'),
            'target_shift3'         => $p('target_shift3'),
            'extra_target_shifts'   => $this->buildExtraShiftsJson(),
            'waktu_awal_realisasi'  => $p('waktu_awal_realisasi'),
            'waktu_akhir_realisasi' => $p('waktu_akhir_realisasi'),
            'realisasi_shift1'      => $p('realisasi_shift1'),
            'realisasi_shift2'      => $p('realisasi_shift2'),
            'realisasi_shift3'      => $p('realisasi_shift3'),
            'revisi'                => $p('revisi'),
            'total_spk'             => $p('total_spk'),
            'packing'               => $p('packing'),
            'keterangan'            => $p('keterangan'),
        ];
    }

    private function buildExtraShiftsJson(): string|null
    {
        $labels  = $this->request->getPost('extra_shift_label')  ?? [];
        $targets = $this->request->getPost('extra_shift_target') ?? [];
        $labels  = is_array($labels)  ? $labels  : [];
        $targets = is_array($targets) ? $targets : [];

        $extraShifts = [];
        foreach ($labels as $i => $label) {
            $label  = trim((string) $label);
            $target = trim((string) ($targets[$i] ?? ''));
            if ($label !== '' || $target !== '') {
                $extraShifts[] = ['label' => $label, 'target' => $target];
            }
        }

        return !empty($extraShifts) ? json_encode($extraShifts, JSON_UNESCAPED_UNICODE) : null;
    }

    private function attachProduksiRealisasi(array $rows): array
    {
        $spkCandidates = static function ($raw): array {
            $str  = trim((string) $raw);
            $intv = (string) (int) $str;
            $cands = array_values(array_unique(array_filter([$str, $intv], fn($s) => $s !== '' && $s !== '0')));
            return $cands;
        };

        foreach ($rows as &$row) {
            $row['produksi_realisasi_shift1'] = null;
            $row['produksi_realisasi_shift2'] = null;
            $row['produksi_realisasi_shift3'] = null;
            $row['produksi_has_data']         = false;
        }
        unset($row);

        $allSpkCandidates = [];
        $allTanggals      = [];
        $allNomorMesin    = [];

        foreach ($rows as $row) {
            $spk     = trim((string) ($row['no_spk'] ?? ''));
            $tanggal = trim((string) ($row['tanggal'] ?? ''));
            $mesin   = trim((string) ($row['nomor_mesin'] ?? ''));

            if ($tanggal === '') {
                continue;
            }

            foreach ($spkCandidates($spk) as $c) {
                $allSpkCandidates[$c] = true;
            }
            $allTanggals[$tanggal] = true;
            if ($mesin !== '') {
                $allNomorMesin[$mesin] = true;
            }
        }

        if (empty($allTanggals)) {
            return $rows;
        }

        try {
            $db       = $this->dbProduksi();
            $tanggals = array_keys($allTanggals);
            $spkList  = array_keys($allSpkCandidates);
            $intSpks  = array_values(array_unique(array_filter(
                array_map(fn($s) => (string)(int)$s, $spkList),
                fn($s) => $s !== '0' && $s !== ''
            )));

            $bySpk = [];

            if (!empty($spkList)) {
                $inPlaceholders  = implode(',', array_fill(0, count($spkList), '?'));
                $castPlaceholders = !empty($intSpks)
                    ? implode(',', array_fill(0, count($intSpks), '?'))
                    : null;
                $tPlaceholders   = implode(',', array_fill(0, count($tanggals), '?'));

                $castClause = $castPlaceholders
                    ? "OR CAST(nomor_spk AS UNSIGNED) IN ($castPlaceholders)"
                    : '';

                $sql = "
                    SELECT nomor_spk, tanggal, shift, SUM(grand_total_bagus) AS total_bagus
                    FROM produksi_headers
                    WHERE deleted_at IS NULL AND tanggal IN ($tPlaceholders)
                      AND (nomor_spk IN ($inPlaceholders) $castClause)
                    GROUP BY nomor_spk, tanggal, shift
                ";

                $bindings = array_merge($tanggals, $spkList, $castPlaceholders ? $intSpks : []);
                $rows1 = $db->query($sql, $bindings)->getResultArray();

                foreach ($rows1 as $r) {
                    $normKey = (string)(int)trim((string)$r['nomor_spk']);
                    $key     = "$normKey|{$r['tanggal']}|{$r['shift']}";
                    $bySpk[$key] = ($bySpk[$key] ?? 0) + (int)$r['total_bagus'];
                }
            }

            $byMesin = [];

            if (!empty($allNomorMesin) && !empty($tanggals)) {
                $mesinList     = array_keys($allNomorMesin);
                $tPlaceholders = implode(',', array_fill(0, count($tanggals), '?'));
                $likeConditions = implode(' OR ', array_map(fn($m) => 'nomor_mesin LIKE ?', $mesinList));

                $sql2 = "
                    SELECT nomor_mesin, tanggal, shift, SUM(grand_total_bagus) AS total_bagus
                    FROM produksi_headers
                    WHERE deleted_at IS NULL AND tanggal IN ($tPlaceholders) AND ($likeConditions)
                    GROUP BY nomor_mesin, tanggal, shift
                ";

                $likePatterns = array_map(fn($m) => "%$m%", $mesinList);
                $bindings     = array_merge($tanggals, $likePatterns);

                $rows2 = $db->query($sql2, $bindings)->getResultArray();

                foreach ($rows2 as $r) {
                    $key           = "{$r['nomor_mesin']}|{$r['tanggal']}|{$r['shift']}";
                    $byMesin[$key] = ($byMesin[$key] ?? 0) + (int)$r['total_bagus'];
                }
            }

            foreach ($rows as &$row) {
                $spk     = trim((string) ($row['no_spk'] ?? ''));
                $tanggal = trim((string) ($row['tanggal'] ?? ''));
                $mesin   = trim((string) ($row['nomor_mesin'] ?? ''));
                $normSpk = (string)(int)$spk;
                $found = false;

                for ($shift = 1; $shift <= 3; $shift++) {
                    $key = "$normSpk|$tanggal|$shift";
                    if (isset($bySpk[$key])) {
                        $row["produksi_realisasi_shift$shift"] = $bySpk[$key];
                        $found = true;
                    }
                }

                if (!$found && $mesin !== '') {
                    for ($shift = 1; $shift <= 3; $shift++) {
                        foreach ($byMesin as $byMesinKey => $byMesinVal) {
                            [$storedMesin, $storedTanggal, $storedShift] = explode('|', $byMesinKey);
                            if ($storedTanggal === $tanggal && (int)$storedShift === (int)$shift
                                && stripos($storedMesin, $mesin) !== false) {
                                $row["produksi_realisasi_shift$shift"] = $byMesinVal;
                                $found = true;
                                break;
                            }
                        }
                    }
                }

                $row['produksi_has_data'] = $found;
            }
            unset($row);

        } catch (\Throwable $e) {
        }

        return $rows;
    }

}
