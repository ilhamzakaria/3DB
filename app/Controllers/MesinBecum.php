<?php

namespace App\Controllers;

use App\Controllers\Traits\MesinModuleTrait;
use App\Controllers\Traits\MesinBrandManagementTrait;
use App\Controllers\Traits\MesinKomposisiManagementTrait;
use App\Models\MMesinBecum;
use App\Models\MPpicMesinBecum;
use App\Models\MMesinBrand;
use App\Models\MMesinKomposisi;

class MesinBecum extends BaseController
{
    use MesinModuleTrait, MesinBrandManagementTrait, MesinKomposisiManagementTrait;

    private const VIEW_PATH = 'gudang/mesinbecum';
    private const STATUS_OPTIONS = ['menggiling', 'bahan_masuk', 'bahan_keluar', 'Ganti packing'];

    private const BECUM_PRODUCTS = ['Gallon'];
    private const BECUM_GRADES = [
        'A+', 'A', 'A.1.V-3', 'A.1.V-4', 'Club', 'Club.', 'A.1.v-3', 'B', 'B.2.B-2', 'B.1.B-2', 'B.1.B-3 New', 'B.1.B-3', 'B-1', 'B.2.B-3', 'B.3.B-2', 'B.', 'B+', 'C.2.B-3', 'C', 'C.3.B-2', 'C.1.B-3', 'C.1.V-2', 'C.1.V-3', 'C+', 'D.0.V-2', 'D', 'D.0.B-2', 'D+', 'D New', 'D.0.V-2 New', 'AQUA',
    ];
    private const BECUM_BRANDS = [
        'A+' => ['8', 'AQ-8', 'Ajwa', 'Ambiero', 'AGS', 'AIRO', 'Al- Falah', 'Arthess', 'Armio', 'Amidis', 'Astar', 'Atarin', 'Aruma', 'Authentic Alkali Water', 'Benzano', 'Blank', 'Bright', 'Cirill', 'Cairoh', 'Cool Plus', 'Dufia', 'El- Haadi', 'Good Qua', 'Grand', 'Hills', 'Jam', 'Java', 'Keniro', 'Ki Water', 'MQS', 'Maleo', 'Meriba', 'Oksitra', 'Patraqua', 'Premier Water', 'PrimA', 'Pristine', 'Quelle', 'RNI', 'SMS', 'SPA', 'NARITA', 'OXYINDO', 'TRIPANCA', 'T Water', 'Watermed', 'Yora'],
        'A' => ['2Tang', 'Adikarya Okane', 'Aguaria', 'Airros', 'AIA', 'Al-AIN', 'Airqu', 'AJS', 'Aekta108', 'AlMasoem', 'Amidis Bandung', 'Amidis Semarang', 'Aqnu', 'Aquanab', 'Ardelia', 'Arona', 'Arlo', 'Arra', 'Aruma', 'Batik', 'Benzano', 'Bismi', 'Blue Oase', 'Bolesa', 'C181', 'Cairoh', 'Citra', 'Club 750', 'Cool Plus', 'Desqua', 'Dufia', 'Elana', 'Fajar', 'Fresh', 'Good Qua', 'Grand', 'Gunung', 'HOKI899', 'Hanaang', 'Hanun', 'Jam', 'Jatiluhur', 'Jeera', 'Jesslyn', 'JQ', 'Karya Umat', 'Khoyiir', 'Kiitafit', 'Lamira', 'MQS', 'Mountoya', 'New Tiara', 'NISA OXY', 'Ocean', 'Origen', 'Oso', 'Oso Nouveo', 'Oxxydav', 'Ozoniri', 'Patisa', 'Patraqua', 'Petriqua', 'PrimA Oro', 'PrimA NS', 'Premier Water', 'Pristine', 'Quat', 'Quelle', 'Q Water', 'Ron88', 'Sankey', 'Suli 5', 'UIN', 'Vania', 'Watermed', 'WaterPlus', 'Water Store', 'Yoji'],
        'A.1.V-3' => ['168', 'Aguaria', 'AQ-8', 'Aerzhips', 'AMC', 'Arthess', 'Ayya', 'Ayonia', 'Ben Ora Salit', 'Blank', 'Blank (Frozen)', 'Blank (Kitafit)', 'Blank (Qfast)', 'DT Fresh', 'Ervina', 'Frozen', 'Hills', 'Kalia', 'Keniro', 'Meriba', 'OXYINDO', 'Pelangi', 'Pulo Air', 'VIVA Celebrity', 'Royal', 'Sajuak', 'Sehat Berkah', 'Sipp', 'TDS NOL', 'TGM99', 'The Living Water', 'Blank TDS', 'Tirta Intan', 'Ubarakah', 'U-Maxx', 'VANN', 'Wigo', 'Zen'],
        'A.1.V-4' => ['Albil', 'Eazy', 'Hudro', 'Indomaret', 'Ramus'],
        'Club' => ['Club Cianjur', 'Club BTA'],
        'Club.' => ['Club Banjarmasin', 'Club Pandaan', 'Club Bogor', 'Club Pontianak', 'Club Palembang', 'Club BOJA'],
        'A.1.v-3' => ['Oksitra'],
        'B' => ['2Tang', 'AEKTA 108', 'Arthess', 'Arvit', 'Atharo', 'Aguaria', 'Al- Masoem', 'AMC \'', 'ASLI', 'Value Plus', 'Bright', 'Bahari', 'Benzano', 'Bolesa', 'Cirill', 'Dufia', 'Elqua', 'Fajar', 'Frozen', 'Grand', 'Great', 'HASS', 'Hanun', 'Hanaang', 'Hatsa', 'Hoki 899', 'JQ', 'Ma soem Arias"', 'Min-Ral', 'Oimantika', 'Pajajaran', 'Pelangi', 'Petriqua', 'Pretty', 'Quazam', 'Ramus', 'SPA', 'Hudro', 'Scintilla', 'Suli 5', 'Jati Luhur', 'Tripanca', 'UFIA', 'Vica', 'Vann', 'VR 9', 'Zodiak'],
        'B.2.B-2' => ['Aerzhips', 'Air Cool', 'Arqu', 'Ahyaqua', 'AIA', 'Blank', 'Ben Ora Salit', 'BW', 'Celesty', 'Citrabass', 'Claras', 'EAZY', 'Gunung', 'GMes', 'Kiitafit', 'Ki Water', 'Premier Water', 'Quelle', 'Ron88', 'Sierra', 'Kalia', 'Ayya'],
        'B.1.B-2' => ['AL- AIN', 'Blank'],
        'B.1.B-3 New' => ['Amidis Droptest', 'Amidis Bandung', 'Amidis Bawen', 'Amidis Pandaan'],
        'B.1.B-3' => ['Amidis Bawen', 'Amidis Pandaan'],
        'B-1' => ['Amidis', 'Amidis (Droptest)'],
        'B.2.B-3' => ['Ayya'],
        'B.3.B-2' => ['LeeQuil', 'Shimizu', 'UFIA'],
        'B.' => ['Qiana', 'Quat'],
        'B+' => ['Umaxx'],
        'C.2.B-3' => ['212', '2 Tang', 'AIA', 'Aquaran', 'Ayya', 'BISTO', 'Blank', 'Bolesa', 'DD Water', 'Delqua', 'Ghamir', 'Gunung', 'Pelangi', 'O Senangnya"', 'Ma soem Arias"', 'Quazam', 'UIN Cyber', 'Viz', 'Winro', 'Zayan'],
        'C' => ['Adea', 'Airiz', 'Airness', 'Akuasis', 'Arthess', 'Avi', 'Benair', 'BMC', 'Bintang Mulia', '700', 'Bumdes', 'Cirill', 'Citra', 'Citrabass', 'Club', 'Cool Plus', 'Eazy', 'Edina', 'Elqua', 'Estolia', 'Fujiro', 'HIDO', 'Inzon', 'Jatiluhur', 'Kiwater', 'Lamira', 'New Normal', 'Petriqua', 'Prisma', 'OK Oce', 'Montoya', 'Quat', 'Quelle', 'Salimdo', 'Sankey', 'Sofia', 'Super 5', 'Tirmas', 'Ufia', 'UIN SGD', 'Viro', 'Viva Celebrity', 'VTG', 'Yqua', 'Zodiak'],
        'C.3.B-2' => ['Aekta108', 'Andalas', 'Vivian'],
        'C.1.B-3' => ['Al Masoem', 'AMC', 'Blio', 'BW', 'Pristine TML/JT', 'Pristine BDG (MAM)', 'Pristine All Plant', 'Pristine DROPTEST', 'Pulo Air', 'Sajuak', 'Viona', 'Bumi Siliwangi'],
        'C.1.V-2' => ['MPP', 'BLU 9', 'Hills', 'U-MAXX'],
        'C.1.V-3' => ['Blank', 'Blio', 'Hevitro'],
        'C+' => ['Pristine Astro', 'Quavit'],
        'D.0.V-2' => ['168', 'AEKTA108', 'Berton', 'BW', 'Citra', 'Edura', 'FreshG', 'Hevitro', 'Hido', 'Jatiluhur', 'LeeQuil', 'Prisma', 'Blank', '(MMP)', 'Qiana', 'VIZ', 'Viva Celebrity', 'Water Store'],
        'D' => ['AGUA', 'Airmu', 'Air Sehat', 'Aquatha', 'Atamin', 'Kausar', 'AMC', 'AMS 318', 'Airiz', 'BMC', 'Bonanza', 'Cirill', 'Estolia', 'Genio', 'Good Qua', 'MDI', 'MMG', 'MC Water', 'Passy', 'PULO AIR', 'Ocean', 'OKE OCE', 'Q-Bic', 'Ravella', 'Sama', 'Senang', 'Vica'],
        'D.0.B-2' => ['Aquality', '(Asabaland)', 'Club Bogor - UI', 'Club Ciranjang/ cianjur', 'Dufia', 'Fountain', 'Great', 'Lamira', 'Lucky water', 'LW8', 'Maskua', 'Mountoya', 'Prisma', 'Blank', '(Biru)', 'Sajuak', 'Tripanca', 'Water Store', 'TMP', 'Wigo'],
        'D+' => ['Club Palembang', 'Club Boja'],
        'D New' => ['Quavit'],
        'D.0.V-2 New' => ['Summit'],
        'AQUA' => ['AQUA'],
    ];

    protected $MMesin;
    protected $MMesinPpic;

    protected function mesinSlug(): string
    {
        return 'mesin_becum';
    }

    public function __construct()
    {
        $this->MMesin     = new MMesinBecum();
        $this->MMesinPpic = new MPpicMesinBecum();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');

        $query = $this->activeMesin();

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('produk_galon', $q)
                ->orLike('brand', $q)
                ->orLike('grade', $q)
                ->orLike('berat', $q)
                ->orLike('stock', $q)
                ->orLike('nomor_lot', $q)
                ->orLike('status', $q)
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

        $data_list = $query
            ->orderBy('tanggal', 'DESC')
            ->orderBy('created_at', 'DESC')
            ->paginate(15, $this->mesinSlug());

        // When in PPIC context, attach realisasi data from produksi_headers
        if ($this->isPpicContext() && !empty($data_list)) {
            $data_list = $this->attachProduksiRealisasi($data_list);
        }

        return view(self::VIEW_PATH, array_merge([
            'title'          => 'Mesin Becum',
            'data_list'      => $data_list,
            'pager'          => $this->activeMesin()->pager,
            'q'              => $q,
            'start_date'     => $startDate,
            'end_date'       => $endDate,
            'current_machine'=> $this->mesinSlug(),
            'becum_products' => self::BECUM_PRODUCTS,
            'becum_grades'   => self::BECUM_GRADES,
            'becum_brands'   => (new MMesinBrand())->getAllGroupedByGrade('becum'),
            'becum_komposisi'=> (new MMesinKomposisi())->getAllByModule('becum'),
            'status_options' => self::STATUS_OPTIONS,
        ], $this->mesinViewContext()));
    }

    public function tambahData()
    {
        if (!$this->request->is('post')) {
            return redirect()->to($this->indexRoute());
        }

        $data = $this->getBecumFields();

        if ($this->activeMesin()->insert($data)) {
            session()->setFlashdata('success', 'Data mesin becum berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data');
        }

        return redirect()->to($this->indexRoute());
    }

    public function update($id = null)
    {
        if (!$this->request->is('post')) {
            return redirect()->to($this->indexRoute());
        }

        $id = $id ?: $this->request->getPost('id');
        $data = $this->getBecumFields();

        if ($this->activeMesin()->update($id, $data)) {
            session()->setFlashdata('success', 'Data mesin becum berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data');
        }

        return redirect()->to($this->indexRoute());
    }

    /**
     * Collect all POST fields for becum — used by both tambahData and update.
     * datetime-local inputs arrive as "yyyy-MM-ddTHH:mm", stored as-is (VARCHAR).
     *
     * Dynamic extra shifts arrive as:
     *   extra_shift_label[]  — e.g. ["Shift 4", "Shift Malam"]
     *   extra_shift_target[] — e.g. ["500", "300"]
     * They are merged into a JSON array stored in extra_target_shifts.
     */
    private function getBecumFields(): array
    {
        $p = fn(string $key) => trim((string) $this->request->getPost($key));
        $pnull = fn(string $key) => ($this->request->getPost($key) !== '' && $this->request->getPost($key) !== null)
            ? trim((string) $this->request->getPost($key))
            : null;

        // Build extra shifts JSON from dynamic POST arrays
        $extraShiftsJson = null;
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
        if (!empty($extraShifts)) {
            $extraShiftsJson = json_encode($extraShifts, JSON_UNESCAPED_UNICODE);
        }

        return [
            // identitas
            'tanggal'               => $p('tanggal') ?: date('Y-m-d'),
            'no_spk'                => $pnull('no_spk') !== null ? (int) $pnull('no_spk') : null,
            'nomor_mesin'           => $p('nomor_mesin'),
            'shif'                  => $p('shif'),
            'status'                => $p('status') ?: 'ganti bottom rata',
            'jam_awal'              => $pnull('jam_awal'),
            'jam_akhir'             => $pnull('jam_akhir'),
            // produk
            'produk_galon'          => $p('produk_galon'),
            'grade'                 => $p('grade'),
            'brand'                 => $p('brand'),
            'berat'                 => $p('berat'),
            // waktu & jumlah target
            'jam'                   => $p('jam'),                   // waktu awal target
            'waktu_akhir_target'    => $p('waktu_akhir_target'),
            'target_shift1'         => $p('target_shift1'),
            'target_shift2'         => $p('target_shift2'),
            'target_shift3'         => $p('target_shift3'),
            'extra_target_shifts'   => $extraShiftsJson,
            // waktu & jumlah realisasi
            'waktu_awal_realisasi'  => $p('waktu_awal_realisasi'),
            'waktu_akhir_realisasi' => $p('waktu_akhir_realisasi'),
            'realisasi_shift1'      => $p('realisasi_shift1'),
            'realisasi_shift2'      => $p('realisasi_shift2'),
            'realisasi_shift3'      => $p('realisasi_shift3'),
            // info tambahan
            'keterangan'            => $p('keterangan'),
            'revisi'                => $p('revisi'),                // komposisi
            'total_spk'             => $p('total_spk'),
            'packing'               => $p('packing'),
            'selesai_spk'           => $this->request->getPost('selesai_spk') ? 1 : 0,
        ];
    }

    /**
     * Attach per-shift realisasi totals from produksi_headers to each mesin row.
     *
     * Matching strategy (in order of priority):
     *  1. PRIMARY:  nomor_spk (varchar, any format) + tanggal + shift
     *               — handles "2926", "02926", " 2926 ", etc. via multi-candidate IN list
     *               — plus CAST fallback for purely numeric SPKs stored differently
     *  2. FALLBACK: nomor_mesin (LIKE match, partial) + tanggal + shift when no SPK match is found
     *
     * The result key `produksi_realisasi_shift{1|2|3}` is ALWAYS present on every
     * row (null = no data found) so the view never gets "Undefined array key".
     */
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
                // mesin_becum vs produksi_headers.
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
            // Uses LIKE for flexible matching (nomor_mesin stored as "becum"
            // matches produksi's "becum 001" via LIKE '%becum%')
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
            // Query 3 — EXTRA SHIFTS: collect all extra shift realisasi per
            // SPK+tanggal+extra_shift_label.
            // Extra shifts are stored in produksi_headers.shift column with pattern
            // like "extra_Shift_4", "extra_Malam", etc.
            // -----------------------------------------------------------------
            // ["normSpk|extraShiftLabel"] => int total  (no tanggal filter)
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
                        shift,
                        SUM(grand_total_bagus) AS total_bagus
                    FROM produksi_headers
                    WHERE deleted_at IS NULL
                      AND shift LIKE 'extra_%'
                      AND (nomor_spk IN ($inPlaceholders) $castClause)
                    GROUP BY nomor_spk, shift
                ";

                $bindings3 = array_merge(
                    $spkList,
                    $castPlaceholders ? $intSpks : []
                );

                $rows3 = $db->query($sql3, $bindings3)->getResultArray();

                foreach ($rows3 as $r) {
                    $normKey    = (string)(int)trim((string)$r['nomor_spk']);
                    $shiftLabel = trim((string)$r['shift']);
                    $key        = "$normKey|$shiftLabel";
                    $byExtraShift[$key] = ($byExtraShift[$key] ?? 0) + (int)$r['total_bagus'];
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
