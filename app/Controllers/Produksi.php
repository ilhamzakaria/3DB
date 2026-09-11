<?php

namespace App\Controllers;

use App\Models\MPpic;
use App\Models\MProduksiHeader;
use App\Models\MProduksiJam;
use App\Models\MProduksiReject;
use App\Models\MProduksiMaterial;
use App\Models\MProduksiColorant;
use App\Models\MProduksiPackaging;
use App\Models\MProduksiDowntime;
use App\Models\MRejectType;

class Produksi extends BaseController
{
    protected $ppicModel;
    protected $headerModel;
    protected $jamModel;
    protected $rejectModel;
    protected $materialModel;
    protected $colorantModel;
    protected $packagingModel;
    protected $downtimeModel;
    protected $rejectTypeModel;

    public function __construct()
    {
        $this->ppicModel = new MPpic();
        $this->headerModel = new MProduksiHeader();
        $this->jamModel = new MProduksiJam();
        $this->rejectModel = new MProduksiReject();
        $this->materialModel = new MProduksiMaterial();
        $this->colorantModel = new MProduksiColorant();
        $this->packagingModel = new MProduksiPackaging();
        $this->downtimeModel = new MProduksiDowntime();
        $this->rejectTypeModel = new MRejectType();
        helper(['form', 'url']);
    }

    // Mesin slug → display label map (shared with views)
    public static function mesinLabels(): array
    {
        return [
            'becum'     => 'Becum',
            'powerjet1' => 'Powerjet 1',
            'powerjet2' => 'Powerjet 2',
            'ccm1'      => 'CCM 1',
            'ccm2'      => 'CCM 2',
            'ips1'      => 'IPS 1',
            'ips2'      => 'IPS 2',
            'ips3'      => 'IPS 3',
            'ips4'      => 'IPS 4',
        ];
    }

    /**
     * Kembalikan daftar slug mesin yang diizinkan untuk suatu role plant.
     * Null berarti role tersebut boleh akses semua mesin.
     */
    public static function getPlantMesin(string $role): ?array
    {
        return match($role) {
            'leader_plant1'        => ['becum', 'powerjet1', 'powerjet2'],
            'leader_plant2'        => ['ccm1', 'ccm2', 'ips1', 'ips2', 'ips3', 'ips4'],
            'operator_produksi1'   => ['becum', 'powerjet1', 'powerjet2'],
            'operator_produksi2'   => ['ccm1', 'ccm2', 'ips1', 'ips2', 'ips3', 'ips4'],
            default                => null,
        };
    }

    /**
     * Cek apakah role memiliki akses ke mesin slug tertentu.
     * Role tanpa batasan plant (null) selalu return true.
     */
    public static function canAccessMesin(string $role, string $mesinSlug): bool
    {
        $allowed = self::getPlantMesin($role);
        return $allowed === null || in_array($mesinSlug, $allowed, true);
    }

    /**
     * Filter a full spk_list down to only SPKs belonging to a specific mesin.
     *
     * The `nama_mesin` stored in each SPK entry is the human label (e.g. "Becum",
     * "IPS 1") — we compare against the canonical label from mesinLabels().
     *
     * @param array  $spkList   Full list returned by getSpkList()
     * @param string $mesinSlug e.g. "becum", "ips1"
     * @return array
     */
    private function filterSpkListByMesin(array $spkList, string $mesinSlug): array
    {
        $labels = self::mesinLabels();
        if (!isset($labels[$mesinSlug])) {
            return $spkList; // unknown slug → return all (safe fallback)
        }

        $label = strtolower(trim($labels[$mesinSlug])); // e.g. "becum", "ccm 2"

        return array_values(array_filter($spkList, static function (array $spk) use ($label): bool {
            $mesinInSpk = strtolower(trim((string) ($spk['nama_mesin'] ?? '')));
            // Exact match or case-insensitive match
            return $mesinInSpk === $label || strtolower($mesinInSpk) === strtolower($label);
        }));
    }

    /**
     * Dedicated mesin sub-page: /produksi/mesin_{slug}
     * Mirrors how PPIC handles /ppic/mesin_{slug} — shows a page scoped to one machine.
     *
     * @param string $mesinSlug  e.g. "becum", "ips1"
     */
    public function mesinIndex(string $mesinSlug = '')
    {
        $labels = self::mesinLabels();

        // Guard: unknown slug → redirect to main produksi page
        if (!isset($labels[$mesinSlug])) {
            return redirect()->to('/produksi');
        }

        // Guard: leader_plant tidak boleh akses mesin di luar plant-nya
        $role = session('role');
        if (!self::canAccessMesin($role, $mesinSlug)) {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses ke mesin ini.');
        }

        $mesinLabel = $labels[$mesinSlug];

        $q       = $this->request->getGet('q');
        $shift   = $this->request->getGet('shift');
        $tanggal = $this->request->getGet('tanggal');
        $filter  = $this->request->getGet('filter');

        $query = $this->headerModel->where('nama_mesin', $mesinLabel);

        // Time-based filtering
        if ($filter === 'day') {
            $query->where('tanggal', date('Y-m-d'));
        } elseif ($filter === 'week') {
            $query->where('tanggal >=', date('Y-m-d', strtotime('-7 days')));
        } elseif ($filter === 'month') {
            $query->where('tanggal >=', date('Y-m-d', strtotime('-30 days')));
        }

        if ($q) {
            $query->groupStart()
                ->like('nomor_spk', $q)
                ->orLike('nama_produk', $q)
                ->orLike('operator', $q)
                ->groupEnd();
        }

        if ($shift) {
            $query->where('shift', $shift);
        }

        if ($tanggal) {
            $query->where('tanggal', $tanggal);
        }

        $data = [
            'title'              => 'Produksi — ' . $mesinLabel,
            'mesin_slug'         => $mesinSlug,
            'mesin_label'        => $mesinLabel,
            'produksi'           => $query->orderBy('tanggal', 'DESC')->orderBy('shift', 'DESC')->orderBy('id', 'DESC')->paginate(15, 'produksi'),
            'pager'              => $this->headerModel->pager,
            // SPK list filtered to this mesin only — only Becum SPKs show on the Becum page, etc.
            'spk_list'           => $this->filterSpkListByMesin($this->getSpkList(), $mesinSlug),
            'reject_types'       => $this->rejectTypeModel->getActive(),
            'context_mesin_slug' => $mesinSlug,   // tells the add modal to pre-set the mesin field
            'q'                  => $q,
            'filter_shift'       => $shift,
            'filter_tanggal'     => $tanggal,
            'filter'             => $filter,
            'filter_mesin'       => $mesinSlug,   // keeps toolbar/table partials working
            'mesin_labels'       => $labels,
            'modern_layout'      => true,
        ];

        return view('produksi/mesin', $data);
    }

    public function index()
    {
        $q       = $this->request->getGet('q');
        $shift   = $this->request->getGet('shift');
        $tanggal = $this->request->getGet('tanggal');
        $filter  = $this->request->getGet('filter');
        $mesin   = $this->request->getGet('mesin'); // e.g. 'becum', 'ips1', …

        // Validate mesin slug to prevent arbitrary DB queries
        $validMesin = array_keys(self::mesinLabels());
        if ($mesin && !in_array($mesin, $validMesin, true)) {
            $mesin = null;
        }

        $query = $this->headerModel;

        // Filter by mesin — nama_mesin column stores human labels like "Becum", "IPS 1" …
        $role       = session('role');
        $plantMesin = self::getPlantMesin($role); // null = akses semua

        if ($plantMesin !== null) {
            // Leader plant: abaikan filter ?mesin dari GET, paksa ke mesin plant ini saja
            $plantLabels = array_values(array_intersect_key(self::mesinLabels(), array_flip($plantMesin)));
            $query->whereIn('nama_mesin', $plantLabels);
        } elseif ($mesin) {
            $label = self::mesinLabels()[$mesin];
            $query->where('nama_mesin', $label);
        }

        // Time-based filtering
        if ($filter === 'day') {
            $query->where('tanggal', date('Y-m-d'));
        } elseif ($filter === 'week') {
            $query->where('tanggal >=', date('Y-m-d', strtotime('-7 days')));
        } elseif ($filter === 'month') {
            $query->where('tanggal >=', date('Y-m-d', strtotime('-30 days')));
        }

        if ($q) {
            $query = $query->groupStart()
                ->like('nomor_spk', $q)
                ->orLike('nama_mesin', $q)
                ->orLike('nama_produk', $q)
                ->orLike('operator', $q)
                ->groupEnd();
        }

        if ($shift) {
            $query = $query->where('shift', $shift);
        }

        if ($tanggal) {
            $query = $query->where('tanggal', $tanggal);
        }

        $data = [
            'title'          => 'Produksi',
            'produksi'       => $query->orderBy('tanggal', 'DESC')->orderBy('shift', 'DESC')->orderBy('id', 'DESC')->paginate(15, 'produksi'),
            'pager'          => $this->headerModel->pager,
            'spk_list'       => $this->getSpkList(),
            'reject_types'   => $this->rejectTypeModel->getActive(),
            'q'              => $q,
            'filter_shift'   => $shift,
            'filter_tanggal' => $tanggal,
            'filter'         => $filter,
            'filter_mesin'   => $mesin,
            'mesin_labels'   => self::mesinLabels(),
            'modern_layout'  => true,
        ];
        return view('produksi/index', $data);
    }

    public function get_edit_form($id)
    {
        $data['header'] = $this->headerModel->find($id);
        if (!$data['header']) return "Data tidak ditemukan.";

        $data['jams'] = $this->jamModel->where('produksi_header_id', $id)->findAll();
        $data['rejects'] = $this->rejectModel->where('produksi_header_id', $id)->findAll();
        $data['materials'] = $this->materialModel->where('produksi_header_id', $id)->findAll();
        $data['colorants'] = $this->colorantModel->where('produksi_header_id', $id)->findAll();
        $data['packaging'] = $this->packagingModel->where('produksi_header_id', $id)->first();
        $data['downtimes'] = $this->downtimeModel->where('produksi_header_id', $id)->findAll();
        $data['reject_types'] = $this->rejectTypeModel->getActive();

        // Filter SPK list to only the mesin matching this record
        $mesinLabel = strtolower(trim((string)($data['header']['nama_mesin'] ?? '')));
        $mesinSlug  = array_search($data['header']['nama_mesin'] ?? '', self::mesinLabels());
        $allSpk     = $this->getSpkList();
        $data['spk_list'] = ($mesinSlug !== false)
            ? $this->filterSpkListByMesin($allSpk, $mesinSlug)
            : array_values(array_filter($allSpk, fn($s) => strtolower(trim($s['nama_mesin'] ?? '')) === $mesinLabel));

        return view('produksi/modals/edit', $data);
    }

    public function get_detail_modal($id)
    {
        $data['header'] = $this->headerModel->find($id);
        if (!$data['header']) return "Data tidak ditemukan.";

        $data['jams']      = $this->jamModel->where('produksi_header_id', $id)->findAll();
        $data['rejects']   = $this->rejectModel->where('produksi_header_id', $id)->findAll();
        $data['materials'] = $this->materialModel->where('produksi_header_id', $id)->findAll();
        $data['colorants'] = $this->colorantModel->where('produksi_header_id', $id)->findAll();
        $data['packaging'] = $this->packagingModel->where('produksi_header_id', $id)->first();
        $data['downtimes'] = $this->downtimeModel->where('produksi_header_id', $id)->findAll();

        // Ambil nomor_mesin dari PPIC berdasarkan nomor_spk
        $data['ppic_nomor_mesin'] = '';
        $noSpk = trim((string)($data['header']['nomor_spk'] ?? ''));
        if ($noSpk !== '') {
            $mesinModels = [
                'App\\Models\\MPpicMesinBecum',
                'App\\Models\\MPpicMesinPowerjet1',
                'App\\Models\\MPpicMesinPowerjet2',
                'App\\Models\\MPpicMesinCcm1',
                'App\\Models\\MPpicMesinCcm2',
                'App\\Models\\MPpicMesinIps1',
                'App\\Models\\MPpicMesinIps2',
                'App\\Models\\MPpicMesinIps3',
                'App\\Models\\MPpicMesinIps4',
            ];
            $candidates = array_values(array_unique(array_filter(
                [$noSpk, (string)(int)$noSpk],
                static fn($s) => $s !== '' && $s !== '0'
            )));
            foreach ($mesinModels as $modelClass) {
                if (!class_exists($modelClass)) continue;
                try {
                    $row = (new $modelClass())->whereIn('no_spk', $candidates)->first();
                    if ($row && !empty($row['nomor_mesin'])) {
                        $data['ppic_nomor_mesin'] = $row['nomor_mesin'];
                        break;
                    }
                } catch (\Throwable $e) { /* skip */ }
            }
        }

        return view('produksi/modals/detail', $data);
    }


    public function simpan()
    {
        if (!in_array(session('role'), ['manager', 'produksi', 'manager_warehouse2', 'operator', 'operator_produksi', 'operator_produksi1', 'operator_produksi2'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah data.');
        }

        // operator_produksi1/2: hanya boleh simpan untuk mesin plant mereka
        $plantMesinOp = self::getPlantMesin(session('role'));
        if ($plantMesinOp !== null) {
            $namaAkanDisimpan = $this->request->getPost('nama_mesin');
            $mesinSlugAkan    = array_search($namaAkanDisimpan, self::mesinLabels());
            if ($mesinSlugAkan === false || !in_array($mesinSlugAkan, $plantMesinOp, true)) {
                return redirect()->back()->with('error', 'Anda hanya dapat menambah data untuk mesin plant Anda.');
            }
        }

        try {
            $db = $this->dbProduksi();
            $db->transStart();

            // 1. Save Header
            $shiftValue = trim($this->request->getPost('shift') ?: $this->request->getPost('shift_hidden'));
            $tercapaiValue = (int)($this->request->getPost('tercapai') ?: '0');
            $gumpalanValue = $this->request->getPost('gumpalan') ?: '0';

            $headerData = [
                'nomor_spk'     => $this->request->getPost('nomor_spk'),
                'nama_mesin'    => $this->request->getPost('nama_mesin'),
                'nama_produk'   => $this->request->getPost('nama_produk'),
                'batch_number'  => $this->request->getPost('batch_number'),
                'type_produk'   => $this->request->getPost('type_produk') ?: null,
                'shift'         => $shiftValue,
                'grup'          => $this->request->getPost('grup'),
                'nomor_mesin'   => $this->request->getPost('nomor_mesin'),
                'packing'       => $this->request->getPost('packing'),
                'cycle_time'    => $this->request->getPost('cycle_time'),
                'target'        => $this->request->getPost('target'),
                'total_spk'     => $this->request->getPost('total_spk') ?: 0,
                'isi'           => $this->request->getPost('isi'),
                'tanggal'       => $this->request->getPost('tanggal'),
                'operator'      => $this->request->getPost('operator'),
                'catatan'       => $this->request->getPost('catatan'),
                'sisa_po'       => $this->request->getPost('sisa_po') ?: 0,
                'hold'          => $this->request->getPost('hold') ?: 0,
                'gumpalan'      => $gumpalanValue,
                'plash_kotor'   => $this->request->getPost('plash_kotor') ?: null,
                'sapuan'        => $this->request->getPost('sapuan') ?: null,
                'tercapai'      => $tercapaiValue,
                'grand_total_bagus'  => $this->request->getPost('grand_total_bagus') ?: 0,
                'grand_total_reject' => $this->request->getPost('grand_total_reject') ?: 0,
                'auto_user_ttd'      => $this->request->getPost('auto_user_ttd') // TTD operator auto-filled berdasarkan shift
            ];

            // Handle Uploads
            $headerData['ttd_shift_1'] = $this->handleUpload('ttd_shift_1');
            $headerData['ttd_shift_2'] = $this->handleUpload('ttd_shift_2');
            $headerData['ttd_shift_3'] = $this->handleUpload('ttd_shift_3');
            $headerData['ttd_spv']     = $this->handleUpload('ttd_spv');

            $headerId = $this->headerModel->insert($headerData);

            // Guard: jika insert header gagal (misal koneksi DB hosting bermasalah), hentikan proses
            if ($headerId === false || $headerId === null || $headerId === 0) {
                $db->transRollback();
                $errors = $this->headerModel->errors();
                $dbError = $this->headerModel->db->error();
                $errorMsg = 'Gagal menyimpan header produksi.';
                $errorDetails = [];

                if (!empty($dbError['message'])) {
                    $errorMsg .= ' DB Error: ' . $dbError['message'];
                    $errorMsg .= ' (Code: ' . ($dbError['code'] ?? 'unknown') . ')';
                    $errorDetails[] = 'DB Error: ' . $dbError['message'];
                }

                if (!empty($errors)) {
                    $validationErrors = is_array($errors) ? implode(', ', $errors) : (string)$errors;
                    $errorMsg .= ' Validation: ' . $validationErrors;
                    $errorDetails = $errors;
                }

                // Log dengan detail lengkap untuk debugging
                log_message('error', '=== Produksi::simpan - INSERT HEADER FAILED ===');
                log_message('error', 'Error Message: ' . $errorMsg);
                log_message('error', 'DB Error Details: ' . json_encode($dbError));
                log_message('error', 'Validation Errors: ' . json_encode($errors));
                log_message('error', 'Header Data Sent: ' . json_encode($headerData));
                log_message('error', 'POST Data: ' . json_encode($this->request->getPost()));
                log_message('error', '=== END ERROR LOG ===');

                // Tampilkan error yang lebih informatif ke user
                return redirect()->back()->withInput()->with('error', $errorMsg);
            }

            // 2. Save Jam
            $jams = $this->request->getPost('jam_data');
            if ($jams) {
                foreach ($jams as $jam) {
                    if (empty($jam['rentang_jam'])) continue;
                    $this->jamModel->insert([
                        'produksi_header_id' => $headerId,
                        'rentang_jam'        => $jam['rentang_jam'],
                        'hasil_produksi'     => $jam['hasil_produksi'] ?? 0,
                    ]);
                }
            }

            // 2b. Save Shift-level Rejects
            $rejects = $this->request->getPost('rejects');
            if ($rejects) {
                // Handle both old format (key-value) and new format (array of objects)
                foreach ($rejects as $key => $rejectData) {
                    // New format: rejects[idx][jenis_reject] and rejects[idx][jumlah]
                    if (is_array($rejectData) && isset($rejectData['jenis_reject']) && isset($rejectData['jumlah'])) {
                        $jumlah = (int)$rejectData['jumlah'];
                        $jenis = $rejectData['jenis_reject'];
                    } else {
                        // Old format: rejects[jenis] = jumlah
                        $jenis = $key;
                        $jumlah = (int)$rejectData;
                    }

                    if (!empty($jenis) && $jumlah > 0) {
                        $this->rejectModel->insert([
                            'produksi_header_id' => $headerId,
                            'produksi_jam_id'    => null, // Shift level
                            'jenis_reject'       => $jenis,
                            'jumlah'             => $jumlah
                        ]);
                    }
                }
            }

            // 3. Save Materials
            $materials = $this->request->getPost('materials');
            if ($materials) {
                foreach ($materials as $mat) {
                    $this->materialModel->insert([
                        'produksi_header_id' => $headerId,
                        'merek_kode'         => $mat['merek_kode'],
                        'pemakaian'          => $mat['pemakaian'],
                        'lot_a'              => $mat['lot_a'],
                        'lot_b'              => $mat['lot_b'],
                        'lot_c'              => $mat['lot_c'],
                        'lot_d'              => $mat['lot_d'],
                        'komposisi_nama_1'   => $mat['komposisi_nama_1'] ?? null,
                        'komposisi_nama_2'   => $mat['komposisi_nama_2'] ?? null,
                        'komposisi_nama_3'   => $mat['komposisi_nama_3'] ?? null,
                        'komposisi_nama_4'   => $mat['komposisi_nama_4'] ?? null,
                        'komposisi_persen_1' => $mat['komposisi_persen_1'] ?? 0,
                        'komposisi_persen_2' => $mat['komposisi_persen_2'] ?? 0,
                        'komposisi_persen_3' => $mat['komposisi_persen_3'] ?? 0,
                        'komposisi_persen_4' => $mat['komposisi_persen_4'] ?? 0,
                        'komposisi_kg_1'     => ($mat['komposisi_kg_1'] ?? '') !== '' ? $mat['komposisi_kg_1'] : null,
                        'komposisi_kg_2'     => ($mat['komposisi_kg_2'] ?? '') !== '' ? $mat['komposisi_kg_2'] : null,
                        'komposisi_kg_3'     => ($mat['komposisi_kg_3'] ?? '') !== '' ? $mat['komposisi_kg_3'] : null,
                        'komposisi_kg_4'     => ($mat['komposisi_kg_4'] ?? '') !== '' ? $mat['komposisi_kg_4'] : null,
                    ]);
                }
            }

            // 4. Save Colorants
            $colorants = $this->request->getPost('colorants');
            if ($colorants) {
                foreach ($colorants as $col) {
                    // TB in/out: untuk Becum berupa array, untuk mesin lain berupa string
                    $tbIn  = null;
                    $tbOut = null;
                    $tbInDetails  = null;
                    $tbOutDetails = null;

                    if (isset($col['tb_in']) && is_array($col['tb_in'])) {
                        $tbInDetails = json_encode(array_values($col['tb_in']));
                    } else {
                        $tbIn = $col['tb_in'] ?? null;
                    }

                    if (isset($col['tb_out']) && is_array($col['tb_out'])) {
                        $tbOutDetails = json_encode(array_values($col['tb_out']));
                    } else {
                        $tbOut = $col['tb_out'] ?? null;
                    }

                    $this->colorantModel->insert([
                        'produksi_header_id' => $headerId,
                        'code'               => $col['code'],
                        'nomor_lot'          => $col['nomor_lot'],
                        'pemakaian'          => $col['pemakaian'],
                        'tb_in'              => $tbIn,
                        'tb_out'             => $tbOut,
                        'tb_in_details'      => $tbInDetails,
                        'tb_out_details'     => $tbOutDetails,
                    ]);
                }
            }

            // 5. Save Packaging
            $this->packagingModel->insert([
                'produksi_header_id'    => $headerId,
                'box'                   => $this->request->getPost('box'),
                'box_ukuran'            => $this->request->getPost('box_ukuran'),
                'plastik'               => $this->request->getPost('plastik'),
                'plastik_bagus'         => $this->request->getPost('plastik_bagus'),
                'plastik_reject'        => $this->request->getPost('plastik_reject'),
                'plastik_ukuran'        => $this->request->getPost('plastik_ukuran'),
                'karung'                => $this->request->getPost('karung'),
                'karung_ukuran'         => $this->request->getPost('karung_ukuran'),
                'nicktainer'            => $this->request->getPost('nicktainer'),
                'nicktainer_ukuran'     => $this->request->getPost('nicktainer_ukuran'),
                'box_karung_nicktainer' => $this->request->getPost('box_karung_nicktainer'),
                'meter_awal'            => $this->request->getPost('meter_awal')      ?: null,
                'meter_akhir'           => $this->request->getPost('meter_akhir')     ?: null,
                'meter_pemakaian'       => $this->request->getPost('meter_pemakaian') ?: null,
            ]);

            // 6. Save Downtimes
            $totalDowntime = 0;
            $downtimes = $this->request->getPost('downtimes');
            if ($downtimes) {
                foreach ($downtimes as $dt) {
                    $this->downtimeModel->insert([
                        'produksi_header_id' => $headerId,
                        'alasan_downtime'    => $dt['alasan'],
                        'problem'            => $dt['problem'] ?? null,
                        'action_plant'       => $dt['action_plant'] ?? null,
                        'waktu_mulai'        => $dt['mulai'],
                        'waktu_selesai'      => $dt['selesai'],
                        'durasi_menit'       => $dt['durasi'],
                    ]);
                    $totalDowntime += (int)$dt['durasi'];
                }
            }

            // Update Downtime in Header
            $this->headerModel->update($headerId, [
                'total_downtime'     => $totalDowntime
            ]);

            $db->transComplete();

            if ($db->transStatus() === false) {
                log_message('error', 'Produksi::simpan - Transaction failed');
                return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data produksi. Transaction rollback.');
            }

            return redirect()->to('/produksi')->with('success', 'Data produksi berhasil disimpan.');
        } catch (\Throwable $e) {
            // Tangkap semua error termasuk fatal error
            log_message('error', 'Produksi::simpan - Exception caught: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());

            return redirect()->back()->withInput()->with(
                'error',
                'Error: ' . $e->getMessage() . ' | Mohon hubungi admin jika masalah berlanjut.'
            );
        }
    }


    public function update($id)
    {
        $role = session('role');

        if (!in_array($role, ['admin', 'manager', 'leader', 'leader_plant1', 'leader_plant2', 'produksi', 'manager_warehouse2'])) {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses untuk mengubah data.');
        }

        // leader_plant: pastikan record yang diedit milik mesin plant mereka
        $plantMesin = self::getPlantMesin($role);
        if ($plantMesin !== null) {
            $header = $this->headerModel->find($id);
            if (!$header) {
                return redirect()->to('/produksi')->with('error', 'Data tidak ditemukan.');
            }
            $mesinSlug = array_search($header['nama_mesin'] ?? '', self::mesinLabels());
            if ($mesinSlug === false || !in_array($mesinSlug, $plantMesin, true)) {
                return redirect()->to('/produksi')->with('error', 'Anda tidak dapat mengedit data mesin plant lain.');
            }
        }

        $db = $this->dbProduksi();
        $db->transStart();

        // 1. Update Header
        $shiftValue = trim($this->request->getPost('shift') ?: $this->request->getPost('shift_hidden'));
        $tercapaiValue = (int)($this->request->getPost('tercapai') ?: '0');
        $gumpalanValue = $this->request->getPost('gumpalan') ?: '0';

        $headerData = [
            'nomor_spk'     => $this->request->getPost('nomor_spk'),
            'nama_mesin'    => $this->request->getPost('nama_mesin'),
            'nama_produk'   => $this->request->getPost('nama_produk'),
            'batch_number'  => $this->request->getPost('batch_number'),
            'type_produk'   => $this->request->getPost('type_produk') ?: null,
            'shift'         => $shiftValue,
            'grup'          => $this->request->getPost('grup'),
            'nomor_mesin'   => $this->request->getPost('nomor_mesin'),
            'packing'       => $this->request->getPost('packing'),
            'cycle_time'    => $this->request->getPost('cycle_time'),
            'target'        => $this->request->getPost('target'),
            'total_spk'     => $this->request->getPost('total_spk') ?: 0,
            'isi'           => $this->request->getPost('isi'),
            'tanggal'       => $this->request->getPost('tanggal'),
            'operator'      => $this->request->getPost('operator'),
            'catatan'       => $this->request->getPost('catatan'),
            'sisa_po'       => $this->request->getPost('sisa_po') ?: 0,
            'hold'          => $this->request->getPost('hold') ?: 0,
            'gumpalan'      => $gumpalanValue,
            'plash_kotor'   => $this->request->getPost('plash_kotor') ?: null,
            'sapuan'        => $this->request->getPost('sapuan') ?: null,
            'tercapai'      => $tercapaiValue,
            'grand_total_bagus'  => $this->request->getPost('grand_total_bagus') ?: 0,
            'grand_total_reject' => $this->request->getPost('grand_total_reject') ?: 0,
            'auto_user_ttd'      => $this->request->getPost('auto_user_ttd') // TTD operator auto-filled berdasarkan shift
        ];

        // Handle Uploads if new files provided
        $ttd1 = $this->handleUpload('ttd_shift_1');
        if ($ttd1) $headerData['ttd_shift_1'] = $ttd1;

        $ttd2 = $this->handleUpload('ttd_shift_2');
        if ($ttd2) $headerData['ttd_shift_2'] = $ttd2;

        $ttd3 = $this->handleUpload('ttd_shift_3');
        if ($ttd3) $headerData['ttd_shift_3'] = $ttd3;

        $ttdspv = $this->handleUpload('ttd_spv');
        if ($ttdspv) $headerData['ttd_spv'] = $ttdspv;

        $this->headerModel->update($id, $headerData);

        // 2. Refresh Jam (Delete & Re-insert)
        $this->jamModel->where('produksi_header_id', $id)->delete();
        $jams = $this->request->getPost('jam_data');
        if ($jams) {
            foreach ($jams as $jam) {
                if (empty($jam['rentang_jam'])) continue;
                $this->jamModel->insert([
                    'produksi_header_id' => $id,
                    'rentang_jam'        => $jam['rentang_jam'],
                    'hasil_produksi'     => $jam['hasil_produksi'] ?? 0,
                ]);
            }
        }

        // 3. Refresh Rejects
        $this->rejectModel->where('produksi_header_id', $id)->delete();
        $rejects = $this->request->getPost('rejects');
        if ($rejects) {
            // Handle both old format (key-value) and new format (array of objects)
            foreach ($rejects as $key => $rejectData) {
                // New format: rejects[idx][jenis_reject] and rejects[idx][jumlah]
                if (is_array($rejectData) && isset($rejectData['jenis_reject']) && isset($rejectData['jumlah'])) {
                    $jumlah = (int)$rejectData['jumlah'];
                    $jenis = $rejectData['jenis_reject'];
                } else {
                    // Old format: rejects[jenis] = jumlah
                    $jenis = $key;
                    $jumlah = (int)$rejectData;
                }

                if (!empty($jenis) && $jumlah > 0) {
                    $this->rejectModel->insert([
                        'produksi_header_id' => $id,
                        'produksi_jam_id'    => null,
                        'jenis_reject'       => $jenis,
                        'jumlah'             => $jumlah
                    ]);
                }
            }
        }

        // 4. Refresh Materials
        $this->materialModel->where('produksi_header_id', $id)->delete();
        $materials = $this->request->getPost('materials');
        if ($materials) {
            foreach ($materials as $mat) {
                $this->materialModel->insert([
                    'produksi_header_id' => $id,
                    'merek_kode'         => $mat['merek_kode'],
                    'pemakaian'          => $mat['pemakaian'],
                    'lot_a'              => $mat['lot_a'],
                    'lot_b'              => $mat['lot_b'],
                    'lot_c'              => $mat['lot_c'],
                    'lot_d'              => $mat['lot_d'],
                    'komposisi_nama_1'   => $mat['komposisi_nama_1'] ?? null,
                    'komposisi_nama_2'   => $mat['komposisi_nama_2'] ?? null,
                    'komposisi_nama_3'   => $mat['komposisi_nama_3'] ?? null,
                    'komposisi_nama_4'   => $mat['komposisi_nama_4'] ?? null,
                    'komposisi_persen_1' => $mat['komposisi_persen_1'] ?? 0,
                    'komposisi_persen_2' => $mat['komposisi_persen_2'] ?? 0,
                    'komposisi_persen_3' => $mat['komposisi_persen_3'] ?? 0,
                    'komposisi_persen_4' => $mat['komposisi_persen_4'] ?? 0,
                    'komposisi_kg_1'     => ($mat['komposisi_kg_1'] ?? '') !== '' ? $mat['komposisi_kg_1'] : null,
                    'komposisi_kg_2'     => ($mat['komposisi_kg_2'] ?? '') !== '' ? $mat['komposisi_kg_2'] : null,
                    'komposisi_kg_3'     => ($mat['komposisi_kg_3'] ?? '') !== '' ? $mat['komposisi_kg_3'] : null,
                    'komposisi_kg_4'     => ($mat['komposisi_kg_4'] ?? '') !== '' ? $mat['komposisi_kg_4'] : null,
                ]);
            }
        }

        // 5. Refresh Colorants
        $this->colorantModel->where('produksi_header_id', $id)->delete();
        $colorants = $this->request->getPost('colorants');
        if ($colorants) {
            foreach ($colorants as $col) {
                // TB in/out: untuk Becum berupa array, untuk mesin lain berupa string
                $tbIn  = null;
                $tbOut = null;
                $tbInDetails  = null;
                $tbOutDetails = null;

                if (isset($col['tb_in']) && is_array($col['tb_in'])) {
                    $tbInDetails = json_encode(array_values($col['tb_in']));
                } else {
                    $tbIn = $col['tb_in'] ?? null;
                }

                if (isset($col['tb_out']) && is_array($col['tb_out'])) {
                    $tbOutDetails = json_encode(array_values($col['tb_out']));
                } else {
                    $tbOut = $col['tb_out'] ?? null;
                }

                $this->colorantModel->insert([
                    'produksi_header_id' => $id,
                    'code'               => $col['code'],
                    'nomor_lot'          => $col['nomor_lot'],
                    'pemakaian'          => $col['pemakaian'],
                    'tb_in'              => $tbIn,
                    'tb_out'             => $tbOut,
                    'tb_in_details'      => $tbInDetails,
                    'tb_out_details'     => $tbOutDetails,
                ]);
            }
        }

        // 6. Refresh Packaging
        $this->packagingModel->where('produksi_header_id', $id)->delete();
        $this->packagingModel->insert([
            'produksi_header_id'    => $id,
            'box'                   => $this->request->getPost('box'),
            'box_ukuran'            => $this->request->getPost('box_ukuran'),
            'plastik'               => $this->request->getPost('plastik'),
            'plastik_bagus'         => $this->request->getPost('plastik_bagus'),
            'plastik_reject'        => $this->request->getPost('plastik_reject'),
            'plastik_ukuran'        => $this->request->getPost('plastik_ukuran'),
            'karung'                => $this->request->getPost('karung'),
            'karung_ukuran'         => $this->request->getPost('karung_ukuran'),
            'nicktainer'            => $this->request->getPost('nicktainer'),
            'nicktainer_ukuran'     => $this->request->getPost('nicktainer_ukuran'),
            'box_karung_nicktainer' => $this->request->getPost('box_karung_nicktainer'),
            'meter_awal'            => $this->request->getPost('meter_awal')      ?: null,
            'meter_akhir'           => $this->request->getPost('meter_akhir')     ?: null,
            'meter_pemakaian'       => $this->request->getPost('meter_pemakaian') ?: null,
        ]);

        // 7. Refresh Downtimes
        $this->downtimeModel->where('produksi_header_id', $id)->delete();
        $totalDowntime = 0;
        $downtimes = $this->request->getPost('downtimes');
        if ($downtimes) {
            foreach ($downtimes as $dt) {
                $this->downtimeModel->insert([
                    'produksi_header_id' => $id,
                    'alasan_downtime'    => $dt['alasan'],
                    'problem'            => $dt['problem'] ?? null,
                    'action_plant'       => $dt['action_plant'] ?? null,
                    'waktu_mulai'        => $dt['mulai'],
                    'waktu_selesai'      => $dt['selesai'],
                    'durasi_menit'       => $dt['durasi'],
                ]);
                $totalDowntime += (int)$dt['durasi'];
            }
        }

        // Update Downtime in Header
        $this->headerModel->update($id, ['total_downtime' => $totalDowntime]);

        $db->transComplete();

        if ($db->transStatus() === false) {
            return redirect()->back()->with('error', 'Gagal memperbarui data produksi.');
        }

        return redirect()->to('/produksi')->with('success', 'Data produksi berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!in_array(session('role'), ['manager', 'produksi', 'manager_warehouse2'])) {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses untuk menghapus data.');
        }

        $this->headerModel->delete($id);
        return redirect()->to('/produksi')->with('success', 'Data dipindahkan ke tempat sampah.');
    }

    public function trash()
    {
        return redirect()->to('/produksi');
    }

    public function restore($id)
    {
        if (!in_array(session('role'), ['manager', 'produksi', 'manager_warehouse2'])) {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses untuk memulihkan data.');
        }

        // Use direct query to bypass soft delete protection on update
        $db = $this->dbProduksi();
        $db->table('produksi_headers')->where('id', $id)->update(['deleted_at' => null]);

        return redirect()->to('/produksi')->with('success', 'Data berhasil dipulihkan.');
    }

    public function deletePermanent($id)
    {
        if (!in_array(session('role'), ['manager', 'produksi', 'manager_warehouse2'])) {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses untuk menghapus data secara permanen.');
        }

        // Purge the record permanently
        $this->headerModel->delete($id, true);
        return redirect()->to('/produksi')->with('success', 'Data berhasil dihapus permanen.');
    }

    public function get_trash()
    {
        $trash = $this->headerModel->onlyDeleted()->orderBy('deleted_at', 'DESC')->findAll();
        return $this->response->setJSON($trash);
    }

    public function detail($id)
    {
        $header = $this->headerModel->find($id);
        if (!$header) {
            return $this->response->setJSON(['error' => 'Data tidak ditemukan'])->setStatusCode(404);
        }

        $data = [
            'header'    => $header,
            'jams'      => $this->jamModel->where('produksi_header_id', $id)->findAll(),
            'rejects'   => $this->rejectModel->where('produksi_header_id', $id)->findAll(),
            'materials' => $this->materialModel->where('produksi_header_id', $id)->findAll(),
            'colorants' => $this->colorantModel->where('produksi_header_id', $id)->findAll(),
            'packaging' => $this->packagingModel->where('produksi_header_id', $id)->first(),
            'downtimes' => $this->downtimeModel->where('produksi_header_id', $id)->findAll(),
        ];

        return $this->response->setJSON($data);
    }

    public function get_last_data($spk)
    {
        // 1. Get latest header from previous production
        $lastHeader = $this->headerModel->where('nomor_spk', $spk)
            ->orderBy('created_at', 'DESC')
            ->first();

        $result = ['last' => $lastHeader];

        if ($lastHeader) {
            $id = $lastHeader['id'];
            $result['jams'] = $this->jamModel->where('produksi_header_id', $id)->findAll();
            $result['rejects'] = $this->rejectModel->where('produksi_header_id', $id)->findAll();
            $result['materials'] = $this->materialModel->where('produksi_header_id', $id)->findAll();
            $result['colorants'] = $this->colorantModel->where('produksi_header_id', $id)->findAll();
            $result['packaging'] = $this->packagingModel->where('produksi_header_id', $id)->first();
            $result['downtimes'] = $this->downtimeModel->where('produksi_header_id', $id)->findAll();
        } else {
            // Fallback to legacy production table (prod)
            $legacyData = $this->dbProduksi()->table('prod')->where('no_spk', $spk)->orderBy('id', 'DESC')->get()->getRowArray();
            $result['legacy'] = $legacyData;
        }

        return $this->response->setJSON($result);
    }

    /**
     * Strip formatting dari nilai numerik yang disimpan sebagai teks.
     * Contoh: "5.000 pcs" → "5000", "1,500" → "1500", "2000" → "2000"
     */
    private function cleanNumericValue($value): string
    {
        $str = trim((string) $value);
        if ($str === '') return '';
        // Hapus titik ribuan, koma, spasi, dan semua huruf/simbol
        $clean = preg_replace('/[^0-9]/', '', str_replace(['.', ','], '', $str));
        return $clean !== '' ? $clean : '';
    }

    private function getSpkList()
    {
        $spkList = [];

        // Machine models in PPIC database with their display names
        $mesinModels = [
            'MPpicMesinBecum' => 'Becum',
            'MPpicMesinPowerjet1' => 'Powerjet 1',
            'MPpicMesinPowerjet2' => 'Powerjet 2',
            'MPpicMesinCcm1' => 'CCM 1',
            'MPpicMesinCcm2' => 'CCM 2',
            'MPpicMesinIps1' => 'IPS 1',
            'MPpicMesinIps2' => 'IPS 2',
            'MPpicMesinIps3' => 'IPS 3',
            'MPpicMesinIps4' => 'IPS 4',
        ];

        // Step 1: Try to get SPKs from all PPIC mesin tables (include all, even if target reached)
        try {
            foreach ($mesinModels as $modelName => $displayName) {
                try {
                    $fullyQualifiedModelName = 'App\\Models\\' . $modelName;
                    if (!class_exists($fullyQualifiedModelName)) {
                        continue;
                    }

                    $mesinModel = new $fullyQualifiedModelName();
                    // Query all SPKs without status filtering - include completed ones
                    $mesinSpks = $mesinModel
                        ->asArray()
                        ->withDeleted()  // Include soft-deleted SPKs
                        ->where('no_spk !=', null)
                        ->where('no_spk !=', '')
                        ->orderBy('tanggal', 'DESC')
                        ->orderBy('id', 'DESC')
                        ->limit(100)  // Increased limit to get more SPKs
                        ->findAll();

                    if (!empty($mesinSpks)) {
                        foreach ($mesinSpks as $mesin) {
                            $key = $mesin['no_spk'] ?? '';
                            if ($key && !isset($spkList[$key])) {
                                // Get last packing value for this SPK from produksi_headers
                                $lastPacking = $this->headerModel
                                    ->select('packing')
                                    ->where('nomor_spk', $key)
                                    ->orderBy('id', 'DESC')
                                    ->first();

                                // Map fields: PENTING! Coba berbagai nama kolom produk
                                // Karena tiap mesin pakai nama kolom berbeda:
                                // - Becum: produk_galon
                                // - CCM: produk_cap_galon
                                // - IPS: produk_preform
                                // - Powerjet: produk_cap_galon
                                $produkName = $mesin['nama_produk']
                                    ?? $mesin['produk_cap_galon']
                                    ?? $mesin['produk_galon']
                                    ?? $mesin['produk_preform']
                                    ?? '';

                                $mesinName = !empty($mesin['nama_mesin']) ? $mesin['nama_mesin'] : $displayName;
                                $target = !empty($mesin['target']) ? $mesin['target'] : ($mesin['target_shift1'] ?? '');

                                // Ambil packing: prioritas dari produksi_headers (terakhir diinput)
                                $packingValue = $lastPacking['packing'] ?? ($mesin['packing'] ?? '');

                                // Calculate total production for this SPK
                                // Coba ambil dari produksi_headers, fallback ke total_spk di tabel mesin
                                $totalProduction = 0;
                                try {
                                    $productionData = $this->headerModel
                                        ->select('SUM(grand_total_bagus) as total_bagus')
                                        ->where('nomor_spk', $key)
                                        ->first();
                                    $totalProduction = (int)($productionData['total_bagus'] ?? 0);
                                } catch (\Exception $e) {
                                    // DB produksi tidak bisa diakses, fallback ke total_spk di tabel mesin
                                    $totalProduction = (int)($mesin['total_spk'] ?? 0);
                                    log_message('warning', "getSpkList: fallback to total_spk for SPK $key: " . $e->getMessage());
                                }

                                $spkList[$key] = [
                                    'no_spk' => $key,
                                    'tanggal' => $mesin['tanggal'] ?? '',
                                    'nama_mesin' => $mesinName,
                                    'nama_produk' => $produkName,
                                    'brand' => $mesin['brand'] ?? '',
                                    'grade' => $mesin['grade'] ?? '',
                                    'warna' => $mesin['warna'] ?? '',
                                    'nomor_mesin' => $mesin['nomor_mesin'] ?? '',
                                    'shif' => $mesin['shif'] ?? '',
                                    'operator' => $mesin['operator'] ?? '',
                                    'targett' => $target,
                                    'packing' => $packingValue,
                                    'total_spk' => $this->cleanNumericValue($mesin['total_spk'] ?? ''),
                                    'revisi' => $mesin['revisi'] ?? '',
                                    'target_int' => (int)$target,
                                    'total_production' => $totalProduction,
                                    'selesai_spk' => (int)($mesin['selesai_spk'] ?? 0),
                                    'is_completed' => (
                                        (int)($mesin['selesai_spk'] ?? 0) === 1
                                        || ((int)$target > 0 && $totalProduction >= (int)$target)
                                    ),
                                ];
                            }
                        }
                    }
                } catch (\Exception $e) {
                    log_message('debug', "Error querying $modelName: " . $e->getMessage());
                    continue;
                }
            }
        } catch (\Exception $e) {
            log_message('warning', 'Error retrieving from PPIC mesin tables: ' . $e->getMessage());
        }

        // Step 2: Also add SPKs from local produksi_headers table (includes historical/completed SPKs)
        try {
            $produksiSpks = $this->headerModel
                ->asArray()
                ->select('nomor_spk, nama_mesin, nama_produk, grade, warna, nomor_mesin, operator, packing, shift as shif, COALESCE(target, 0) as targett, total_spk, MAX(tanggal) as tanggal, MAX(selesai_spk) as selesai_spk')
                ->where('nomor_spk !=', null)
                ->where('nomor_spk !=', '')
                ->groupBy('nomor_spk')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(100)
                ->findAll();

            if (!empty($produksiSpks)) {
                foreach ($produksiSpks as $prod) {
                    $key = $prod['nomor_spk'] ?? '';
                    if ($key && !isset($spkList[$key])) {
                        // Calculate total production for this SPK
                        $totalProduction = 0;
                        try {
                            $productionData = $this->headerModel
                                ->select('SUM(grand_total_bagus) as total_bagus')
                                ->where('nomor_spk', $key)
                                ->first();
                            $totalProduction = (int)($productionData['total_bagus'] ?? 0);
                        } catch (\Exception $e) {
                            log_message('warning', "getSpkList step2: cannot sum for SPK $key: " . $e->getMessage());
                        }
                        $targetValue = (int)($prod['targett'] ?? 0);

                        $spkList[$key] = [
                            'no_spk' => $key,
                            'tanggal' => $prod['tanggal'] ?? '',
                            'nama_mesin' => $prod['nama_mesin'] ?? '',
                            'nama_produk' => $prod['nama_produk'] ?? '',
                            'brand' => $prod['brand'] ?? '',
                            'grade' => $prod['grade'] ?? '',
                            'warna' => $prod['warna'] ?? '',
                            'nomor_mesin' => $prod['nomor_mesin'] ?? '',
                            'shif' => $prod['shif'] ?? '',
                            'operator' => $prod['operator'] ?? '',
                            'targett' => $prod['targett'] ?? '',
                            'packing' => $prod['packing'] ?? '',
                            'total_spk' => $this->cleanNumericValue($prod['total_spk'] ?? ''),
                            'target_int' => $targetValue,
                            'total_production' => $totalProduction,
                            'selesai_spk' => (int)($prod['selesai_spk'] ?? 0),
                            'is_completed' => (
                                (int)($prod['selesai_spk'] ?? 0) === 1
                                || ($targetValue > 0 && $totalProduction >= $targetValue)
                            ),
                        ];
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('warning', 'Error retrieving SPKs from produksi_headers table: ' . $e->getMessage());
        }

        if (empty($spkList)) {
            log_message('warning', 'No SPK data found from PPIC mesin tables or produksi_headers table');
        } else {
            log_message('info', 'getSpkList returned ' . count($spkList) . ' SPKs');
        }

        // Sort by tanggal DESC (terbaru di atas), fallback ke no_spk DESC
        usort($spkList, function ($a, $b) {
            $tA = $a['tanggal'] ?? '';
            $tB = $b['tanggal'] ?? '';
            if ($tA === $tB) {
                return strnatcasecmp($b['no_spk'], $a['no_spk']); // no_spk DESC
            }
            return strcmp($tB, $tA); // tanggal DESC
        });

        return array_values($spkList);
    }

    /**
     * Get target value for a specific shift from PPIC mesin table.
     * 
     * GET /produksi/get-target-by-shift?mesin=becum&nomor_mesin=B.02&shift=1
     * Shift values: '1', '2', '3', or 'extra_Shift_4' (URL-encoded extra shift key)
     */
    public function getTargetByShift()
    {
        $mesin      = $this->request->getGet('mesin');       // e.g. 'becum'
        $nomorMesin = $this->request->getGet('nomor_mesin');
        $shift      = $this->request->getGet('shift');       // '1', '2', '3', 'extra:Label'
        $noSpk      = trim((string) ($this->request->getGet('no_spk') ?? ''));

        if (!$mesin || !$nomorMesin || !$shift) {
            return $this->response->setJSON(['target' => null, 'error' => 'Missing params']);
        }

        $tableName = 'mesin_' . strtolower($mesin);
        $allowedTables = [
            'mesin_becum',
            'mesin_powerjet1',
            'mesin_powerjet2',
            'mesin_ccm1',
            'mesin_ccm2',
            'mesin_ips1',
            'mesin_ips2',
            'mesin_ips3',
            'mesin_ips4'
        ];

        if (!in_array($tableName, $allowedTables)) {
            return $this->response->setJSON(['target' => null, 'error' => 'Invalid mesin']);
        }

        try {
            $db = db_connect('ppic');
            $builder = $db->table($tableName)
                ->where('nomor_mesin', $nomorMesin)
                ->select('target_shift1, target_shift2, target_shift3, extra_target_shifts')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1);

            // Filter by no_spk jika dikirim — supaya target sesuai SPK yang dipilih
            if ($noSpk !== '') {
                $builder->where('no_spk', $noSpk);
            }

            $row = $builder->get()->getRowArray();

            if (!$row) {
                return $this->response->setJSON(['target' => null, 'message' => 'No data found']);
            }

            $target = null;

            // Map shift values
            if ($shift === '1' && !empty($row['target_shift1'])) {
                $target = $row['target_shift1'];
            } elseif ($shift === '2' && !empty($row['target_shift2'])) {
                $target = $row['target_shift2'];
            } elseif ($shift === '3' && !empty($row['target_shift3'])) {
                $target = $row['target_shift3'];
            } elseif (str_starts_with($shift, 'extra|')) {
                // Extra shift key format: "extra|Label" — pipe-separated
                // e.g. "extra|Shift 4" -> shiftLabel = "Shift 4"
                $shiftLabel = substr($shift, strlen('extra|'));

                if (!empty($row['extra_target_shifts'])) {
                    try {
                        $extras = json_decode($row['extra_target_shifts'], true);
                        if (is_array($extras)) {
                            foreach ($extras as $ex) {
                                $storedLabel = trim($ex['label'] ?? '');
                                // Try exact match first, then case-insensitive
                                if ($storedLabel === $shiftLabel || strtolower($storedLabel) === strtolower($shiftLabel)) {
                                    $target = $ex['target'] ?? null;
                                    break;
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // invalid JSON
                    }
                }
            } elseif (str_starts_with($shift, 'extra:')) {
                // Extra shift key format: "extra:" + rawurlencode(label)
                // e.g. "extra:Shift%204" -> rawurldecode -> "Shift 4"
                $shiftLabel = rawurldecode(substr($shift, strlen('extra:')));

                if (!empty($row['extra_target_shifts'])) {
                    try {
                        $extras = json_decode($row['extra_target_shifts'], true);
                        if (is_array($extras)) {
                            foreach ($extras as $ex) {
                                $storedLabel = trim($ex['label'] ?? '');
                                // Try exact match first, then case-insensitive
                                if ($storedLabel === $shiftLabel || strtolower($storedLabel) === strtolower($shiftLabel)) {
                                    $target = $ex['target'] ?? null;
                                    break;
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // invalid JSON
                    }
                }
            } elseif (str_starts_with($shift, 'extra_')) {
                // Legacy format: "extra_Shift_4" -> strip prefix -> "Shift_4" -> replace "_" -> "Shift 4"
                $withoutPrefix = substr($shift, strlen('extra_'));
                $shiftLabel = str_replace('_', ' ', $withoutPrefix);

                if (!empty($row['extra_target_shifts'])) {
                    try {
                        $extras = json_decode($row['extra_target_shifts'], true);
                        if (is_array($extras)) {
                            foreach ($extras as $ex) {
                                $storedLabel = trim($ex['label'] ?? '');
                                if ($storedLabel === $shiftLabel || strtolower($storedLabel) === strtolower($shiftLabel)) {
                                    $target = $ex['target'] ?? null;
                                    break;
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // invalid JSON
                    }
                }
            }

            return $this->response->setJSON(['target' => $target]);
        } catch (Exception $e) {
            return $this->response->setJSON(['target' => null, 'error' => $e->getMessage()]);
        }
    }

    /**
     * Debug endpoint to check SPK list
     */
    public function getShifts()
    {
        $mesin      = $this->request->getGet('mesin');       // e.g. 'becum', 'ips1'
        $nomorMesin = $this->request->getGet('nomor_mesin');
        $noSpk      = trim((string) ($this->request->getGet('no_spk') ?? ''));

        if (!$mesin || !$nomorMesin) {
            return $this->response->setJSON(['shifts' => [], 'error' => 'Missing params']);
        }

        // Map mesin name to table name
        $tableName = 'mesin_' . strtolower($mesin);
        $allowedTables = [
            'mesin_becum',
            'mesin_powerjet1',
            'mesin_powerjet2',
            'mesin_ccm1',
            'mesin_ccm2',
            'mesin_ips1',
            'mesin_ips2',
            'mesin_ips3',
            'mesin_ips4'
        ];

        if (!in_array($tableName, $allowedTables)) {
            return $this->response->setJSON(['shifts' => [], 'error' => 'Invalid mesin']);
        }

        try {
            $db = db_connect('ppic');
            $builder = $db->table($tableName)
                ->where('nomor_mesin', $nomorMesin)
                ->select('target_shift1, target_shift2, target_shift3, extra_target_shifts');

            // Filter by no_spk jika dikirim — supaya shift yang tampil sesuai SPK yang dipilih
            if ($noSpk !== '') {
                $builder->where('no_spk', $noSpk);
            }

            $rows = $builder->get()->getResultArray();

            $shifts = [];
            $extraShiftSet = []; // deduplicate extras

            // Always add default 1, 2, 3 if any row has them
            foreach ($rows as $row) {
                if (!empty($row['target_shift1'])) $shifts['1'] = 'Shift 1';
                if (!empty($row['target_shift2'])) $shifts['2'] = 'Shift 2';
                if (!empty($row['target_shift3'])) $shifts['3'] = 'Shift 3';

                // Extract extra shifts from JSON
                if (!empty($row['extra_target_shifts'])) {
                    try {
                        $extras = json_decode($row['extra_target_shifts'], true);
                        if (is_array($extras)) {
                            foreach ($extras as $ex) {
                                $label = trim($ex['label'] ?? '');
                                if ($label && !isset($extraShiftSet[$label])) {
                                    $extraShiftSet[$label] = true;
                                    // Use "extra_" prefix followed by index instead of pipe char
                                    // Pipe char causes CodeIgniter routing to reject the URI (400 Bad Request)
                                    // Use index-based key: extra_0, extra_1, etc
                                    $extraKey = 'extra_' . count($extraShiftSet);
                                    $shifts[$extraKey] = $label;
                                }
                            }
                        }
                    } catch (Exception $e) {
                        // invalid JSON, skip
                    }
                }
            }

            // If no rows found or all empty, return default 1/2/3
            if (empty($shifts)) {
                $shifts = ['1' => 'Shift 1', '2' => 'Shift 2', '3' => 'Shift 3'];
            }

            return $this->response->setJSON(['shifts' => $shifts]);
        } catch (Exception $e) {
            return $this->response->setJSON(['shifts' => [], 'error' => $e->getMessage()]);
        }
    }

    /**
     * Debug endpoint to check SPK list
     */
    /**
     * AUTO-TEST endpoint - Test autofill step by step
     * GET /produksi/test_total_spk_autofill
     */
    public function test_total_spk_autofill()
    {
        $results = [
            'timestamp' => date('Y-m-d H:i:s'),
            'step1_database' => [],
            'step2_getspklist' => [],
            'step3_ajax_endpoint' => [],
            'step4_javascript' => []
        ];

        // STEP 1: Check PPIC mesin_becum table
        try {
            $db = db_connect('ppic');
            $mesinData = $db->table('ppic_mesin_becum')
                ->select('no_spk, total_spk, nama_mesin')
                ->where('no_spk IS NOT NULL')
                ->limit(5)
                ->get()
                ->getResultArray();

            $results['step1_database'] = [
                'status' => 'OK',
                'table' => 'ppic_mesin_becum',
                'count' => count($mesinData),
                'has_total_spk_column' => isset($mesinData[0]['total_spk']) ? 'YES' : 'NO',
                'samples' => $mesinData
            ];
        } catch (\Exception $e) {
            $results['step1_database'] = ['status' => 'ERROR', 'message' => $e->getMessage()];
        }

        // STEP 2: Check getSpkList() returns total_spk
        try {
            $spkList = $this->getSpkList();
            $spkWithTotalSpk = array_filter($spkList, fn($s) => !empty($s['total_spk']));

            $results['step2_getspklist'] = [
                'status' => 'OK',
                'total_spks' => count($spkList),
                'with_total_spk_value' => count($spkWithTotalSpk),
                'percentage' => count($spkList) > 0 ? round((count($spkWithTotalSpk) / count($spkList)) * 100, 2) . '%' : 'N/A',
                'sample_with_value' => array_slice(array_values($spkWithTotalSpk), 0, 2),
                'sample_without_value' => array_slice(array_values(array_filter($spkList, fn($s) => empty($s['total_spk']))), 0, 2)
            ];
        } catch (\Exception $e) {
            $results['step2_getspklist'] = ['status' => 'ERROR', 'message' => $e->getMessage()];
        }

        // STEP 3: Test getPpicSpkDetail() AJAX endpoint
        try {
            // Get first SPK from list
            if (!empty($spkList)) {
                $testSpk = $spkList[0]['no_spk'];

                // Simulate AJAX call
                $ppicDetail = null;
                $selesaiStatus = 0;
                $mesinModels = [
                    'App\\Models\\MPpicMesinBecum',
                    'App\\Models\\MPpicMesinPowerjet1',
                    'App\\Models\\MPpicMesinPowerjet2',
                    'App\\Models\\MPpicMesinCcm1',
                    'App\\Models\\MPpicMesinCcm2',
                    'App\\Models\\MPpicMesinIps1',
                    'App\\Models\\MPpicMesinIps2',
                    'App\\Models\\MPpicMesinIps3',
                    'App\\Models\\MPpicMesinIps4',
                ];

                foreach ($mesinModels as $modelClass) {
                    if (!class_exists($modelClass)) continue;
                    $model = new $modelClass();
                    $row = $model->where('no_spk', $testSpk)->first();
                    if ($row) {
                        $ppicDetail = [
                            'no_spk' => $row['no_spk'] ?? $testSpk,
                            'total_spk' => $row['total_spk'] ?? '',
                            'nama_mesin' => $row['nama_mesin'] ?? '',
                        ];
                        break;
                    }
                }

                $results['step3_ajax_endpoint'] = [
                    'status' => $ppicDetail ? 'OK' : 'NO_DATA',
                    'test_spk' => $testSpk,
                    'ppic_detail' => $ppicDetail,
                    'has_total_spk' => $ppicDetail && !empty($ppicDetail['total_spk']) ? 'YES' : 'NO'
                ];
            } else {
                $results['step3_ajax_endpoint'] = ['status' => 'SKIP', 'reason' => 'No SPKs in list'];
            }
        } catch (\Exception $e) {
            $results['step3_ajax_endpoint'] = ['status' => 'ERROR', 'message' => $e->getMessage()];
        }

        // STEP 4: JavaScript info (from server perspective)
        $results['step4_javascript'] = [
            'status' => 'MANUAL_CHECK',
            'instruction' => 'Open browser console and paste: ' .
                'console.log(document.querySelector(".total_spk")?.value, document.querySelector(".total_spk_hidden")?.value)',
            'notes' => 'Check if values sync before form submit'
        ];

        return $this->response->setJSON($results);
    }

    /**
     * DEBUG endpoint - Analyze SPK list and total_spk data
     * GET /produksi/debug_spk_list
     */
    public function debug_spk_list()
    {
        $debugInfo = [
            'ppic_table_count' => 0,
            'mesin_tables' => [],
            'total_spk_analysis' => []
        ];

        // Check main ppic table
        try {
            $count = $this->ppicModel->countAll();
            $debugInfo['ppic_table_count'] = $count;
            $debugInfo['ppic_sample'] = $this->ppicModel->limit(3)->findAll();
        } catch (\Exception $e) {
            $debugInfo['ppic_error'] = $e->getMessage();
        }

        // Check mesin tables
        $mesinModels = [
            'MPpicMesinBecum',
            'MPpicMesinPowerjet1',
            'MPpicMesinPowerjet2',
            'MPpicMesinCcm1',
            'MPpicMesinCcm2',
            'MPpicMesinIps1',
            'MPpicMesinIps2',
            'MPpicMesinIps3',
            'MPpicMesinIps4',
        ];

        foreach ($mesinModels as $modelName) {
            try {
                $fullyQualifiedModelName = 'App\\Models\\' . $modelName;
                if (class_exists($fullyQualifiedModelName)) {
                    $model = new $fullyQualifiedModelName();
                    $count = $model->countAll();
                    $sample = $model->limit(1)->findAll();

                    // Check if sample has total_spk and its value
                    $totalSpkInfo = 'N/A';
                    if (!empty($sample)) {
                        $first = $sample[0];
                        $totalSpkInfo = isset($first['total_spk'])
                            ? ('value: ' . $first['total_spk'])
                            : 'column exists but NULL or empty';
                    }

                    $debugInfo['mesin_tables'][$modelName] = [
                        'count' => $count,
                        'total_spk_status' => $totalSpkInfo,
                        'sample' => $sample
                    ];
                }
            } catch (\Exception $e) {
                $debugInfo['mesin_tables'][$modelName] = ['error' => $e->getMessage()];
            }
        }

        // Get final SPK list
        $spkList = $this->getSpkList();
        $debugInfo['final_spk_list_count'] = count($spkList);

        // Analyze total_spk in final list
        $totalSpkHasValue = 0;
        $totalSpkEmpty = 0;
        if (!empty($spkList)) {
            foreach ($spkList as $spk) {
                if (!empty($spk['total_spk'])) {
                    $totalSpkHasValue++;
                } else {
                    $totalSpkEmpty++;
                }
            }
        }

        $debugInfo['total_spk_analysis'] = [
            'with_value' => $totalSpkHasValue,
            'empty' => $totalSpkEmpty,
            'percentage_filled' => $spkList ? round(($totalSpkHasValue / count($spkList)) * 100, 2) . '%' : 'N/A'
        ];

        $debugInfo['final_spk_list_sample'] = array_slice($spkList, 0, 3);

        return $this->response->setJSON($debugInfo);
    }

    private function handleUpload($fieldName)
    {
        $file = $this->request->getFile($fieldName);
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/ttd';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            return $newName;
        }
        return null;
    }

    /**
     * Get the last packing value used for an SPK from produksi table
     */
    private function getLastPackingForSpk($noSpk)
    {
        if (empty($noSpk)) {
            return '';
        }

        $row = $this->headerModel
            ->select('packing')
            ->where('nomor_spk', $noSpk)
            ->orderBy('created_at', 'DESC')
            ->first();

        return ($row && !empty($row['packing'])) ? $row['packing'] : '';
    }

    /**
     * Return a compact SPK list for use by other modules (e.g. gudang autofill).
     *
     * GET /produksi/get_produksi_spk_list
     *
     * Response: JSON array of { no_spk, nama_produk, shift, operator }
     * Drawn from the existing getSpkList() which aggregates PPIC mesin tables
     * plus produksi_headers, so it's always up to date.
     */
    public function getProduksiSpkList()
    {
        $list = $this->getSpkList();

        // Map to the minimal shape the shared autofill view expects.
        $result = array_map(static function (array $item): array {
            return [
                'no_spk'      => $item['no_spk']      ?? '',
                'nama_produk' => $item['nama_produk']  ?? '',
                'shift'       => $item['shif']         ?? '',
                'operator'    => $item['operator']     ?? '',
            ];
        }, $list);

        return $this->response->setJSON($result);
    }

    /**
     * Return full PPIC + last-produksi detail for one SPK number.
     *
     * GET /produksi/get_ppic_spk/<spk>
     * GET /api/spk-detail/<spk>
     *
     * Response shape mirrors what the Produksi JS autofill expects:
     * {
     *   ppic:         { no_spk, nama_mesin, nama_produk, nomor_mesin, operator,
     *                   targett, shif, packing, grade, warna, selesai_spk },
     *   last:         <latest produksi_headers row or null>,
     *   selesai_spk:  0|1   (convenience top-level flag)
     * }
     */
    public function getPpicSpkDetail($spk = null)
    {
        $spk = trim((string) ($spk ?? $this->request->getGet('spk')));
        if ($spk === '') {
            return $this->response->setJSON(['error' => 'SPK tidak diberikan'])->setStatusCode(400);
        }

        // Build candidate strings (raw + integer form) for broad matching.
        $candidates = array_values(array_unique(array_filter(
            [$spk, (string) (int) $spk],
            static fn($s) => $s !== '' && $s !== '0'
        )));

        $ppicDetail    = null;
        $selesaiStatus = 0;
        $mesinModule   = null;

        // Search across all PPIC mesin tables in priority order.
        $mesinModels = [
            'App\\Models\\MPpicMesinBecum'       => 'becum',
            'App\\Models\\MPpicMesinPowerjet1'   => 'powerjet1',
            'App\\Models\\MPpicMesinPowerjet2'   => 'powerjet2',
            'App\\Models\\MPpicMesinCcm1'        => 'ccm1',
            'App\\Models\\MPpicMesinCcm2'        => 'ccm2',
            'App\\Models\\MPpicMesinIps1'        => 'ips1',
            'App\\Models\\MPpicMesinIps2'        => 'ips2',
            'App\\Models\\MPpicMesinIps3'        => 'ips3',
            'App\\Models\\MPpicMesinIps4'        => 'ips4',
        ];

        foreach ($mesinModels as $modelClass => $moduleKey) {
            if (!class_exists($modelClass)) {
                continue;
            }
            try {
                $model = new $modelClass();
                $row   = $model->whereIn('no_spk', $candidates)
                    ->orderBy('tanggal', 'DESC')
                    ->orderBy('id', 'DESC')
                    ->first();

                if ($row) {
                    // Normalise product-name field — each mesin uses a different column.
                    $namaProduk = $row['nama_produk']
                        ?? $row['produk_galon']
                        ?? $row['produk_cap_galon']
                        ?? $row['produk_preform']
                        ?? '';

                    $ppicDetail = [
                        'no_spk'      => $row['no_spk']      ?? $spk,
                        'nama_mesin'  => $row['nama_mesin']  ?? '',
                        'nama_produk' => $namaProduk,
                        'nomor_mesin' => $row['nomor_mesin'] ?? '',
                        'operator'    => $row['operator']    ?? '',
                        'targett'     => $row['target']      ?? $row['target_shift1'] ?? '',
                        'shif'        => $row['shif']        ?? '',
                        'packing'     => $row['packing']     ?? '',
                        'grade'       => $row['grade']       ?? '',
                        'warna'       => $row['warna']       ?? '',
                        'total_spk'   => $this->cleanNumericValue($row['total_spk'] ?? ''),
                        'revisi'      => $row['revisi']      ?? '',
                        'selesai_spk' => (int) ($row['selesai_spk'] ?? 0),
                    ];
                    $selesaiStatus = $ppicDetail['selesai_spk'];
                    $mesinModule   = $moduleKey; // Store mesin module for komposisi lookup
                    break;
                }
            } catch (\Throwable $e) {
                log_message('warning', "getPpicSpkDetail: error querying {$modelClass}: " . $e->getMessage());
            }
        }

        // Latest produksi_headers row for the same SPK (last-data fallback).
        $lastHeader = null;
        try {
            $lastHeader = $this->headerModel
                ->whereIn('nomor_spk', $candidates)
                ->orderBy('created_at', 'DESC')
                ->first();

            // Prefer selesai_spk from produksi_headers when PPIC row not found.
            if ($lastHeader && $ppicDetail === null) {
                $selesaiStatus = (int) ($lastHeader['selesai_spk'] ?? 0);
            }
        } catch (\Throwable $e) {
            log_message('warning', "getPpicSpkDetail: error querying produksi_headers: " . $e->getMessage());
        }

        // Hitung total grand_total_bagus kumulatif dari semua row produksi_headers
        // yang matching SPK ini (non-deleted). Untuk mode edit, exclude row yg sedang diedit
        // agar tidak double-count produksi shift ini.
        $totalBagusKumulatif = 0;
        $excludeId = (int) ($this->request->getGet('exclude_id') ?? 0);
        try {
            $sumQuery = $this->headerModel
                ->selectSum('grand_total_bagus', 'total')
                ->whereIn('nomor_spk', $candidates);
            if ($excludeId > 0) {
                $sumQuery->where('id !=', $excludeId);
            }
            $sumRow = $sumQuery->first();
            $totalBagusKumulatif = (int) ($sumRow['total'] ?? 0);
        } catch (\Throwable $e) {
            log_message('warning', "getPpicSpkDetail: error summing grand_total_bagus: " . $e->getMessage());
        }

        // Hitung sisa_po awal: total_spk - kumulatif (minimum 0)
        $totalSpkInt = (int) ($ppicDetail['total_spk'] ?? 0);
        $sisaPoAwal  = max(0, $totalSpkInt - $totalBagusKumulatif);

        // Fetch komposisi data jika mesin ditemukan
        $komposisiList = [];
        if ($mesinModule) {
            try {
                $komposisiModel = new \App\Models\MMesinKomposisi();
                $komposisiList = $komposisiModel->getAllByModule($mesinModule);
            } catch (\Throwable $e) {
                log_message('warning', "getPpicSpkDetail: error fetching komposisi for {$mesinModule}: " . $e->getMessage());
            }
        }

        return $this->response->setJSON([
            'ppic'                  => $ppicDetail,
            'last'                  => $lastHeader,
            'selesai_spk'           => $selesaiStatus,
            'total_bagus_kumulatif' => $totalBagusKumulatif,
            'sisa_po_awal'          => $sisaPoAwal,
            'komposisi'             => $komposisiList,
            'revisi'                => $ppicDetail['revisi'] ?? '',
            'mesin_module'          => $mesinModule,
        ]);
    }

    /**
     * API endpoint untuk fetch user TTD berdasarkan shift
     * GET /produksi/get-user-ttd/{shift}
     */
    public function getUserTtd($shift = null)
    {
        // Support query string sebagai fallback (?shift=...) agar aman dari karakter spesial di URL path
        if ($shift === null || $shift === '') {
            $shift = $this->request->getGet('shift') ?? '';
        }

        try {
            log_message('info', "=== getUserTtd called ===");
            log_message('info', "URI: " . $this->request->getUri());
            log_message('info', "Shift parameter: {$shift}");

            // Get current user ID from session
            $userId = session('id_user');
            $username = session('username');

            log_message('info', "Session - userId: {$userId}, username: {$username}");

            if (!$userId) {
                log_message('warning', 'getUserTtd: No id_user in session');
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User tidak ditemukan di session',
                ])->setStatusCode(401);
            }

            // Fetch user data dengan tanda_tangan dari users table
            $userModel = new \App\Models\MUser();
            $user = $userModel->find($userId);

            if (!$user) {
                log_message('warning', "getUserTtd: User {$userId} not found in database");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'User tidak ditemukan di database',
                ])->setStatusCode(404);
            }

            log_message('info', "getUserTtd: User found - {$user['username']}");

            // Get tanda_tangan path dari user profile
            $tandaTanganPath = $user['tanda_tangan'] ?? null;

            log_message('info', "TTD path from DB: {$tandaTanganPath}");

            if (!$tandaTanganPath) {
                log_message('info', "getUserTtd: User {$userId} has no TTD uploaded yet");
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Tanda tangan belum di-upload di profile',
                ])->setStatusCode(200);
            }

            // Return success dengan path tanda tangan
            log_message('info', "getUserTtd: Returning TTD - {$tandaTanganPath}");
            return $this->response->setJSON([
                'success' => true,
                'tanda_tangan' => $tandaTanganPath,
                'username' => $user['username'],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error in getUserTtd: ' . $e->getMessage());
            log_message('error', 'Trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage(),
            ])->setStatusCode(500);
        }
    }
}
