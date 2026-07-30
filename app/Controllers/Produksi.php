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

        $label = strtolower($labels[$mesinSlug]); // e.g. "becum", "ips 1"

        return array_values(array_filter($spkList, static function (array $spk) use ($label): bool {
            $mesinInSpk = strtolower(trim((string) ($spk['nama_mesin'] ?? '')));
            return $mesinInSpk === $label;
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
        if ($mesin) {
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
        
        $data['jams'] = $this->jamModel->where('produksi_header_id', $id)->findAll();
        $data['rejects'] = $this->rejectModel->where('produksi_header_id', $id)->findAll();
        $data['materials'] = $this->materialModel->where('produksi_header_id', $id)->findAll();
        $data['colorants'] = $this->colorantModel->where('produksi_header_id', $id)->findAll();
        $data['packaging'] = $this->packagingModel->where('produksi_header_id', $id)->first();
        $data['downtimes'] = $this->downtimeModel->where('produksi_header_id', $id)->findAll();
        
        return view('produksi/modals/detail', $data);
    }


    public function simpan()
    {
        if (!in_array(session('role'), ['admin', 'manager', 'produksi', 'manager_warehouse2'])) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah data.');
        }

        $db = $this->dbProduksi();
        $db->transStart();

        // 1. Save Header
        $shiftValue = trim($this->request->getPost('shift') ?: $this->request->getPost('shift_hidden'));
        $tercapaiValue = (int)($this->request->getPost('tercapai') ?: '0');
        $headerData = [
            'nomor_spk'     => $this->request->getPost('nomor_spk'),
            'nama_mesin'    => $this->request->getPost('nama_mesin'),
            'nama_produk'   => $this->request->getPost('nama_produk'),
            'batch_number'  => $this->request->getPost('batch_number'),
            'shift'         => $shiftValue,
            'grup'          => $this->request->getPost('grup'),
            'nomor_mesin'   => $this->request->getPost('nomor_mesin'),
            'packing'       => $this->request->getPost('packing'),
            'cycle_time'    => $this->request->getPost('cycle_time'),
            'target'        => $this->request->getPost('target'),
            'isi'           => $this->request->getPost('isi'),
            'tanggal'       => $this->request->getPost('tanggal'),
            'operator'      => $this->request->getPost('operator'),
            'catatan'       => $this->request->getPost('catatan'),
            'sisa_po'       => $this->request->getPost('sisa_po') ?: 0,
            'hold'          => $this->request->getPost('hold') ?: 0,
            'gumpalan'      => $this->request->getPost('gumpalan') ?: 0,
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
            if (!empty($dbError['message'])) {
                $errorMsg .= ' DB: ' . $dbError['message'];
            } elseif (!empty($errors)) {
                $errorMsg .= ' ' . implode(', ', $errors);
            }
            log_message('error', 'Produksi::simpan - insert header gagal: ' . $errorMsg . ' | Data: ' . json_encode($headerData));
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
                ]);
            }
        }

        // 4. Save Colorants
        $colorants = $this->request->getPost('colorants');
        if ($colorants) {
            foreach ($colorants as $col) {
                $this->colorantModel->insert([
                    'produksi_header_id' => $headerId,
                    'code'               => $col['code'],
                    'nomor_lot'          => $col['nomor_lot'],
                    'pemakaian'          => $col['pemakaian'],
                ]);
            }
        }

        // 5. Save Packaging
        $this->packagingModel->insert([
            'produksi_header_id'    => $headerId,
            'box'                   => $this->request->getPost('box'),
            'box_ukuran'            => $this->request->getPost('box_ukuran'),
            'plastik'               => $this->request->getPost('plastik'),
            'plastik_ukuran'        => $this->request->getPost('plastik_ukuran'),
            'karung'                => $this->request->getPost('karung'),
            'karung_ukuran'         => $this->request->getPost('karung_ukuran'),
            'box_karung_nicktainer' => $this->request->getPost('box_karung_nicktainer'),
        ]);

        // 6. Save Downtimes
        $totalDowntime = 0;
        $downtimes = $this->request->getPost('downtimes');
        if ($downtimes) {
            foreach ($downtimes as $dt) {
                $this->downtimeModel->insert([
                    'produksi_header_id' => $headerId,
                    'alasan_downtime'    => $dt['alasan'],
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
            return redirect()->back()->with('error', 'Gagal menyimpan data produksi.');
        }

        return redirect()->to('/produksi')->with('success', 'Data produksi berhasil disimpan.');
    }


    public function update($id)
    {
        if (!in_array(session('role'), ['admin', 'manager', 'produksi', 'manager_warehouse2'])) {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses untuk mengubah data.');
        }

        $db = $this->dbProduksi();
        $db->transStart();

        // 1. Update Header
        $shiftValue = trim($this->request->getPost('shift') ?: $this->request->getPost('shift_hidden'));
        $tercapaiValue = (int)($this->request->getPost('tercapai') ?: '0');
        $headerData = [
            'nomor_spk'     => $this->request->getPost('nomor_spk'),
            'nama_mesin'    => $this->request->getPost('nama_mesin'),
            'nama_produk'   => $this->request->getPost('nama_produk'),
            'batch_number'  => $this->request->getPost('batch_number'),
            'shift'         => $shiftValue,
            'grup'          => $this->request->getPost('grup'),
            'nomor_mesin'   => $this->request->getPost('nomor_mesin'),
            'packing'       => $this->request->getPost('packing'),
            'cycle_time'    => $this->request->getPost('cycle_time'),
            'target'        => $this->request->getPost('target'),
            'isi'           => $this->request->getPost('isi'),
            'tanggal'       => $this->request->getPost('tanggal'),
            'operator'      => $this->request->getPost('operator'),
            'catatan'       => $this->request->getPost('catatan'),
            'sisa_po'       => $this->request->getPost('sisa_po') ?: 0,
            'hold'          => $this->request->getPost('hold') ?: 0,
            'gumpalan'      => $this->request->getPost('gumpalan') ?: 0,
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
                ]);
            }
        }

        // 5. Refresh Colorants
        $this->colorantModel->where('produksi_header_id', $id)->delete();
        $colorants = $this->request->getPost('colorants');
        if ($colorants) {
            foreach ($colorants as $col) {
                $this->colorantModel->insert([
                    'produksi_header_id' => $id,
                    'code'               => $col['code'],
                    'nomor_lot'          => $col['nomor_lot'],
                    'pemakaian'          => $col['pemakaian'],
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
            'plastik_ukuran'        => $this->request->getPost('plastik_ukuran'),
            'karung'                => $this->request->getPost('karung'),
            'karung_ukuran'         => $this->request->getPost('karung_ukuran'),
            'box_karung_nicktainer' => $this->request->getPost('box_karung_nicktainer'),
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
        if (!in_array(session('role'), ['admin', 'manager', 'produksi', 'manager_warehouse2'])) {
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
        if (!in_array(session('role'), ['admin', 'manager', 'produksi', 'manager_warehouse2'])) {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses untuk memulihkan data.');
        }

        // Use direct query to bypass soft delete protection on update
        $db = $this->dbProduksi();
        $db->table('produksi_headers')->where('id', $id)->update(['deleted_at' => null]);
        
        return redirect()->to('/produksi')->with('success', 'Data berhasil dipulihkan.');
    }

    public function deletePermanent($id)
    {
        if (!in_array(session('role'), ['admin', 'manager', 'produksi', 'manager_warehouse2'])) {
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
                                    'grade' => $mesin['grade'] ?? '',
                                    'warna' => $mesin['warna'] ?? '',
                                    'nomor_mesin' => $mesin['nomor_mesin'] ?? '',
                                    'shif' => $mesin['shif'] ?? '',
                                    'operator' => $mesin['operator'] ?? '',
                                    'targett' => $target,
                                    'packing' => $packingValue,
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
                ->select('nomor_spk, nama_mesin, nama_produk, grade, warna, nomor_mesin, operator, packing, shift as shif, COALESCE(target, 0) as targett, MAX(tanggal) as tanggal, MAX(selesai_spk) as selesai_spk')
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
                                'grade' => $prod['grade'] ?? '',
                                'warna' => $prod['warna'] ?? '',
                                'nomor_mesin' => $prod['nomor_mesin'] ?? '',
                                'shif' => $prod['shif'] ?? '',
                                'operator' => $prod['operator'] ?? '',
                                'targett' => $prod['targett'] ?? '',
                                'packing' => $prod['packing'] ?? '',
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
        usort($spkList, function($a, $b) {
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
        $mesin = $this->request->getGet('mesin'); // e.g. 'becum'
        $nomorMesin = $this->request->getGet('nomor_mesin');
        $shift = $this->request->getGet('shift'); // value: '1', '2', '3', atau 'extra_Shift_4'

        if (!$mesin || !$nomorMesin || !$shift) {
            return $this->response->setJSON(['target' => null, 'error' => 'Missing params']);
        }

        $tableName = 'mesin_' . strtolower($mesin);
        $allowedTables = [
            'mesin_becum', 'mesin_powerjet1', 'mesin_powerjet2', 
            'mesin_ccm1', 'mesin_ccm2', 'mesin_ips1', 'mesin_ips2', 'mesin_ips3', 'mesin_ips4'
        ];

        if (!in_array($tableName, $allowedTables)) {
            return $this->response->setJSON(['target' => null, 'error' => 'Invalid mesin']);
        }

        try {
            $db = db_connect('ppic');
            $row = $db->table($tableName)
                ->where('nomor_mesin', $nomorMesin)
                ->select('target_shift1, target_shift2, target_shift3, extra_target_shifts')
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

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
        $mesin = $this->request->getGet('mesin'); // e.g. 'becum', 'ips1'
        $nomorMesin = $this->request->getGet('nomor_mesin');

        if (!$mesin || !$nomorMesin) {
            return $this->response->setJSON(['shifts' => [], 'error' => 'Missing params']);
        }

        // Map mesin name to table name
        $tableName = 'mesin_' . strtolower($mesin);
        $allowedTables = [
            'mesin_becum', 'mesin_powerjet1', 'mesin_powerjet2', 
            'mesin_ccm1', 'mesin_ccm2', 'mesin_ips1', 'mesin_ips2', 'mesin_ips3', 'mesin_ips4'
        ];

        if (!in_array($tableName, $allowedTables)) {
            return $this->response->setJSON(['shifts' => [], 'error' => 'Invalid mesin']);
        }

        try {
            $db = db_connect('ppic');
            $rows = $db->table($tableName)
                ->where('nomor_mesin', $nomorMesin)
                ->select('target_shift1, target_shift2, target_shift3, extra_target_shifts')
                ->get()
                ->getResultArray();

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
    public function debug_spk_list()
    {
        $debugInfo = [
            'ppic_table_count' => 0,
            'mesin_tables' => []
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
                    $debugInfo['mesin_tables'][$modelName] = [
                        'count' => $count,
                        'sample' => $model->limit(1)->findAll()
                    ];
                }
            } catch (\Exception $e) {
                $debugInfo['mesin_tables'][$modelName] = ['error' => $e->getMessage()];
            }
        }

        // Get final SPK list
        $spkList = $this->getSpkList();
        $debugInfo['final_spk_list_count'] = count($spkList);
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

        // Search across all PPIC mesin tables in priority order.
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
                        'selesai_spk' => (int) ($row['selesai_spk'] ?? 0),
                    ];
                    $selesaiStatus = $ppicDetail['selesai_spk'];
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

        return $this->response->setJSON([
            'ppic'        => $ppicDetail,
            'last'        => $lastHeader,
            'selesai_spk' => $selesaiStatus,
        ]);
    }

    /**
     * API endpoint untuk fetch user TTD berdasarkan shift
     * GET /produksi/get-user-ttd/{shift}
     */
    public function getUserTtd($shift = null)
    {
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
