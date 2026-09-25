<?php

namespace App\Controllers;

use App\Controllers\Traits\MesinModuleTrait;
use App\Controllers\Traits\MesinBrandManagementTrait;
use App\Controllers\Traits\MesinKomposisiManagementTrait;
use App\Models\MMesinCcm2;
use App\Models\MPpicMesinCcm2;
use App\Models\MMesinKomposisi;

class MesinCcm2 extends BaseController
{
    use MesinModuleTrait, MesinBrandManagementTrait, MesinKomposisiManagementTrait;

    private const VIEW_PATH = 'gudang/mesinccm2';

    protected $MMesin;
    protected $MMesinPpic;

    protected function mesinSlug(): string { return 'mesin_ccm2'; }

    public function __construct()
    {
        $this->MMesin     = new MMesinCcm2();
        $this->MMesinPpic = new MPpicMesinCcm2();
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
                ->orLike('produk_cap_galon', $q)->orLike('brand', $q)
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
            'title'              => 'Mesin CCM 2',
            'data_list'          => $data_list,
            'pager'              => $this->activeMesin()->pager,
            'q'                  => $q,
            'start_date'         => $startDate,
            'end_date'           => $endDate,
            'current_machine'    => $this->mesinSlug(),
            'ccm2_komposisi'     => (new MMesinKomposisi())->getAllByModule('ccm2'),
        ], $this->mesinViewContext()));
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) return redirect()->to($this->indexRoute());
        if ($this->activeMesin()->insert($this->getCapGalonFields())) {
            session()->setFlashdata('success', 'Data Mesin CCM 2 berhasil ditambahkan');
        } else { session()->setFlashdata('error', 'Gagal menambahkan data'); }
        return redirect()->to($this->indexRoute());
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) return redirect()->to($this->indexRoute());
        $id = $id ?: $this->request->getPost('id');
        if ($this->activeMesin()->update($id, $this->getCapGalonFields())) {
            session()->setFlashdata('success', 'Data Mesin CCM 2 berhasil diupdate');
        } else { session()->setFlashdata('error', 'Gagal mengupdate data'); }
        return redirect()->to($this->indexRoute());
    }

    private function getCapGalonFields(): array
    {
        $p = fn(string $k) => trim((string) $this->request->getPost($k));
        return [
            'tanggal'               => $p('tanggal') ?: date('Y-m-d'),
            'no_spk'                => $p('no_spk') !== '' ? (int) $p('no_spk') : null,
            'nomor_mesin'           => $p('nomor_mesin'),
            'shif'                  => $p('shif'),
            'status'                => $p('status') ?: 'ganti bottom rata',
            'jam_awal'              => $p('jam_awal'),
            'jam_akhir'             => $p('jam_akhir'),
            'produk_cap_galon'      => $p('produk_cap_galon'),
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
        // -- helpers -----------------------------------------------------------

        // Build a set of candidate SPK strings that could appear in produksi_headers
        // for a given no_spk integer. E.g. 2926 → ["2926", "02926"] but also
        // handle the raw string form already stored as-is by the Produksi controller.
        $spkCandidates = static function ($raw): array {
            $str  = trim((string) $raw);
            $intv = (string) (int) $str;           // "2926"
            $cands = array_values(array_unique(array_filter([$str, $intv], fn($s) => $s !== '' && $s !== '0')));
            return $cands;
        };

        // -- pre-initialise all keys on every row (safety net for the view) -----
        foreach ($rows as &$row) {
            $row['produksi_realisasi_shift1'] = null;
            $row['produksi_realisasi_shift2'] = null;
            $row['produksi_realisasi_shift3'] = null;
            $row['extra_realisasi_shifts']    = null;  // JSON array for extra shifts
            $row['produksi_has_data']         = false;  // badge flag for view
        }
        unset($row);

        // -- collect all SPK candidates and tanggals we need to look up ----------
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

            // -----------------------------------------------------------------
            // Query 1 — PRIMARY: aggregate grand_total_bagus per SPK+tanggal+shift
            // Uses a broad IN list of all candidate SPK strings AND a CAST for
            // numeric-stored variants that don't appear verbatim in the candidates.
            // -----------------------------------------------------------------
            $spkList  = array_keys($allSpkCandidates);
            $intSpks  = array_values(array_unique(array_filter(
                array_map(fn($s) => (string)(int)$s, $spkList),
                fn($s) => $s !== '0' && $s !== ''
            )));

            // ["normSpk|shift"] => int total  (no tanggal filter — SPK is unique enough)
            $bySpk = [];

            if (!empty($spkList)) {
                $inPlaceholders   = implode(',', array_fill(0, count($spkList), '?'));
                $castPlaceholders = !empty($intSpks)
                    ? implode(',', array_fill(0, count($intSpks), '?'))
                    : null;

                $castClause = $castPlaceholders
                    ? "OR CAST(nomor_spk AS UNSIGNED) IN ($castPlaceholders)"
                    : '';

                // No tanggal filter here: SPK is unique per job, so matching by SPK
                // alone is correct even when the operator records different dates in
                // mesin_[MESIN] vs produksi_headers.
                $sql = "
                    SELECT
                        nomor_spk,
                        shift,
                        SUM(grand_total_bagus) AS total_bagus
                    FROM produksi_headers
                    WHERE deleted_at IS NULL
                      AND (nomor_spk IN ($inPlaceholders) $castClause)
                    GROUP BY nomor_spk, shift
                ";

                $bindings = array_merge(
                    $spkList,
                    $castPlaceholders ? $intSpks : []
                );

                $rows1 = $db->query($sql, $bindings)->getResultArray();

                foreach ($rows1 as $r) {
                    $normKey = (string)(int)trim((string)$r['nomor_spk']);
                    $key     = "$normKey|{$r['shift']}";
                    $bySpk[$key] = ($bySpk[$key] ?? 0) + (int)$r['total_bagus'];
                }
            }

            // -----------------------------------------------------------------
            // Query 2 — FALLBACK: aggregate by nomor_mesin+tanggal+shift
            // Used only for rows where the SPK lookup produced no data.
            // Uses LIKE for flexible matching (nomor_mesin stored as "[MESIN]"
            // matches produksi's "[MESIN] 001" via LIKE '%[MESIN]%')
            // -----------------------------------------------------------------
            $byMesin = [];  // ["nomor_mesin|tanggal|shift"] => int total

            if (!empty($allNomorMesin) && !empty($tanggals)) {
                // Build a raw query that uses LIKE for each nomor_mesin
                $mesinList     = array_keys($allNomorMesin);
                $tPlaceholders = implode(',', array_fill(0, count($tanggals), '?'));

                // Build LIKE conditions: (nomor_mesin LIKE ? OR nomor_mesin LIKE ? OR ...)
                $likeConditions = implode(' OR ', array_map(fn($m) => 'nomor_mesin LIKE ?', $mesinList));

                $sql2 = "
                    SELECT
                        nomor_mesin,
                        tanggal,
                        shift,
                        SUM(grand_total_bagus) AS total_bagus
                    FROM produksi_headers
                    WHERE deleted_at IS NULL
                      AND tanggal IN ($tPlaceholders)
                      AND ($likeConditions)
                    GROUP BY nomor_mesin, tanggal, shift
                ";

                // Build bindings: tanggals first, then LIKE patterns (%)
                $likePatterns = array_map(fn($m) => "%$m%", $mesinList);
                $bindings     = array_merge($tanggals, $likePatterns);

                $rows2 = $db->query($sql2, $bindings)->getResultArray();

                foreach ($rows2 as $r) {
                    $key           = "{$r['nomor_mesin']}|{$r['tanggal']}|{$r['shift']}";
                    $byMesin[$key] = ($byMesin[$key] ?? 0) + (int)$r['total_bagus'];
                }
            }

            // -----------------------------------------------------------------
            // Query 3 — EXTRA SHIFTS: collect extra shift realisasi from JSON
            // stored in produksi_headers.extra_realisasi_shifts column.
            // -----------------------------------------------------------------
            // ["normSpk|label"] => int total
            $byExtraShift = [];

            if (!empty($spkList)) {
                $inPlaceholders   = implode(',', array_fill(0, count($spkList), '?'));
                $castPlaceholders = !empty($intSpks)
                    ? implode(',', array_fill(0, count($intSpks), '?'))
                    : null;

                $castClause = $castPlaceholders
                    ? "OR CAST(nomor_spk AS UNSIGNED) IN ($castPlaceholders)"
                    : '';

                $sql3 = "
                    SELECT
                        nomor_spk,
                        extra_realisasi_shifts
                    FROM produksi_headers
                    WHERE deleted_at IS NULL
                      AND extra_realisasi_shifts IS NOT NULL
                      AND extra_realisasi_shifts != '[]'
                      AND (nomor_spk IN ($inPlaceholders) $castClause)
                ";

                $bindings3 = array_merge(
                    $spkList,
                    $castPlaceholders ? $intSpks : []
                );

                $rows3 = $db->query($sql3, $bindings3)->getResultArray();

                foreach ($rows3 as $r) {
                    $normKey = (string)(int)trim((string)$r['nomor_spk']);
                    
                    // Decode JSON array of extra shifts
                    try {
                        $extraShifts = json_decode($r['extra_realisasi_shifts'], true);
                        if (is_array($extraShifts)) {
                            foreach ($extraShifts as $extraShift) {
                                if (isset($extraShift['label']) && isset($extraShift['realisasi'])) {
                                    $label = trim((string)$extraShift['label']);
                                    $realisasi = (int)$extraShift['realisasi'];
                                    if (!empty($label) && $realisasi > 0) {
                                        $key = "$normKey|$label";
                                        $byExtraShift[$key] = ($byExtraShift[$key] ?? 0) + $realisasi;
                                    }
                                }
                            }
                        }
                    } catch (\Throwable $e) {
                        // Invalid JSON, skip this row
                    }
                }
            }

            // -----------------------------------------------------------------
            // Attach results to each row
            // -----------------------------------------------------------------
            foreach ($rows as &$row) {
                $spk     = trim((string) ($row['no_spk'] ?? ''));
                $tanggal = trim((string) ($row['tanggal'] ?? ''));
                $mesin   = trim((string) ($row['nomor_mesin'] ?? ''));
                $normSpk = (string)(int)$spk;

                $found = false;

                // Try SPK-based match first (key is now "normSpk|shift" — no tanggal)
                for ($shift = 1; $shift <= 3; $shift++) {
                    $key = "$normSpk|$shift";
                    if (isset($bySpk[$key])) {
                        $row["produksi_realisasi_shift$shift"] = $bySpk[$key];
                        $found = true;
                    }
                }

                // If SPK match found nothing, try nomor_mesin fallback using partial key matching
                if (!$found && $mesin !== '') {
                    for ($shift = 1; $shift <= 3; $shift++) {
                        // Find any byMesin key that contains this mesin string
                        foreach ($byMesin as $byMesinKey => $byMesinVal) {
                            [$storedMesin, $storedTanggal, $storedShift] = explode('|', $byMesinKey);
                            if ($storedTanggal === $tanggal
                                && (int)$storedShift === (int)$shift
                                && stripos($storedMesin, $mesin) !== false) {
                                $row["produksi_realisasi_shift$shift"] = $byMesinVal;
                                $found = true;
                                break;  // Found match for this shift, stop searching
                            }
                        }
                    }
                }

                $row['produksi_has_data'] = $found;

                // --------- EXTRA SHIFTS REALISASI --------
                $extraShiftsData = [];
                if (!empty($byExtraShift)) {
                    foreach ($byExtraShift as $key => $val) {
                        [$keySpk, $keyShift] = explode('|', $key, 2);
                        if ($keySpk === $normSpk) {
                            // Format label: "extra_1" → "Extra Shift 1", "extra_Malam" → "Extra Malam"
                            $suffix = substr($keyShift, strlen('extra_'));
                            $suffix = str_replace('_', ' ', $suffix);
                            $displayLabel = is_numeric(trim($suffix))
                                ? 'Extra Shift ' . trim($suffix)
                                : 'Extra ' . ucfirst($suffix);
                            $extraShiftsData[] = [
                                'label'     => $displayLabel,
                                'realisasi' => $val,
                            ];
                        }
                    }
                }
                if (!empty($extraShiftsData)) {
                    $row['extra_realisasi_shifts'] = json_encode($extraShiftsData);
                }
            }
            unset($row);

        } catch (\Throwable $e) {
            // Produksi DB unavailable — rows already have null/false defaults
        }

        return $rows;
    }

}
