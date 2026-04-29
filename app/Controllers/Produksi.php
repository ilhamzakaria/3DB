<?php

namespace App\Controllers;

use App\Models\MProduksiHeader;
use App\Models\MProduksiJam;
use App\Models\MProduksiReject;
use App\Models\MProduksiMaterial;
use App\Models\MProduksiColorant;
use App\Models\MProduksiPackaging;
use App\Models\MProduksiDowntime;

class Produksi extends BaseController
{
    protected $headerModel;
    protected $jamModel;
    protected $rejectModel;
    protected $materialModel;
    protected $colorantModel;
    protected $packagingModel;
    protected $downtimeModel;

    public function __construct()
    {
        $this->headerModel = new MProduksiHeader();
        $this->jamModel = new MProduksiJam();
        $this->rejectModel = new MProduksiReject();
        $this->materialModel = new MProduksiMaterial();
        $this->colorantModel = new MProduksiColorant();
        $this->packagingModel = new MProduksiPackaging();
        $this->downtimeModel = new MProduksiDowntime();
        helper(['form', 'url']);
    }

    public function index()
    {
        $q = $this->request->getGet('q');
        $shift = $this->request->getGet('shift');
        $tanggal = $this->request->getGet('tanggal');
        $filter = $this->request->getGet('filter');

        $query = $this->headerModel;

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
                           ->orLike('nama_produksi', $q)
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
            'title' => 'Produksi',
            'produksi' => $query->orderBy('tanggal', 'DESC')->orderBy('shift', 'DESC')->paginate(15, 'produksi'),
            'pager' => $this->headerModel->pager,
            'spk_list' => $this->getSpkList(),
            'q' => $q,
            'filter_shift' => $shift,
            'filter_tanggal' => $tanggal,
            'filter' => $filter
        ];
        return view('produksi', $data);
    }


    public function simpan()
    {
        if (session('role') != 'produksi') {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk menambah data.');
        }

        $db = $this->dbProduksi();
        $db->transStart();

        // 1. Save Header
        $headerData = [
            'nomor_spk'     => $this->request->getPost('nomor_spk'),
            'nama_mesin'    => $this->request->getPost('nama_mesin'),
            'nama_produksi' => $this->request->getPost('nama_produksi'),
            'batch_number'  => $this->request->getPost('batch_number'),
            'shift'         => $this->request->getPost('shift'),
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
            'grand_total_bagus'  => $this->request->getPost('grand_total_bagus') ?: 0,
            'grand_total_reject' => $this->request->getPost('grand_total_reject') ?: 0,
        ];

        // Handle Uploads
        $headerData['ttd_shift_1'] = $this->handleUpload('ttd_shift_1');
        $headerData['ttd_shift_2'] = $this->handleUpload('ttd_shift_2');
        $headerData['ttd_shift_3'] = $this->handleUpload('ttd_shift_3');
        $headerData['ttd_spv']     = $this->handleUpload('ttd_spv');

        $headerId = $this->headerModel->insert($headerData);

        // 2. Save Jam
        $jams = $this->request->getPost('jam_data');
        if ($jams) {
            foreach ($jams as $jam) {
                $this->jamModel->insert([
                    'produksi_header_id' => $headerId,
                    'rentang_jam'        => $jam['rentang_jam'],
                    'hasil_produksi'     => $jam['hasil_produksi'],
                ]);
            }
        }

        // 2b. Save Shift-level Rejects
        $rejects = $this->request->getPost('rejects');
        if ($rejects) {
            foreach ($rejects as $jenis => $jumlah) {
                if ($jumlah > 0) {
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
            'box_karung_nicktainer' => $this->request->getPost('box_karung_nicktainer'),
            'plastik'               => $this->request->getPost('plastik'),
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
        if (session('role') != 'produksi') {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses untuk mengubah data.');
        }

        $db = $this->dbProduksi();
        $db->transStart();

        // 1. Update Header
        $headerData = [
            'nomor_spk'     => $this->request->getPost('nomor_spk'),
            'nama_mesin'    => $this->request->getPost('nama_mesin'),
            'nama_produksi' => $this->request->getPost('nama_produksi'),
            'batch_number'  => $this->request->getPost('batch_number'),
            'shift'         => $this->request->getPost('shift'),
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
            'grand_total_bagus'  => $this->request->getPost('grand_total_bagus') ?: 0,
            'grand_total_reject' => $this->request->getPost('grand_total_reject') ?: 0,
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
                $this->jamModel->insert([
                    'produksi_header_id' => $id,
                    'rentang_jam'        => $jam['rentang_jam'],
                    'hasil_produksi'     => $jam['hasil_produksi'],
                ]);
            }
        }

        // 3. Refresh Rejects
        $this->rejectModel->where('produksi_header_id', $id)->delete();
        $rejects = $this->request->getPost('rejects');
        if ($rejects) {
            foreach ($rejects as $jenis => $jumlah) {
                if ($jumlah > 0) {
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
            'box_karung_nicktainer' => $this->request->getPost('box_karung_nicktainer'),
            'plastik'               => $this->request->getPost('plastik'),
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
        if (session('role') != 'produksi') {
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
        if (session('role') != 'produksi') {
            return redirect()->to('/produksi')->with('error', 'Anda tidak memiliki akses untuk memulihkan data.');
        }

        // Use direct query to bypass soft delete protection on update
        $db = $this->dbProduksi();
        $db->table('produksi_headers')->where('id', $id)->update(['deleted_at' => null]);
        
        return redirect()->to('/produksi')->with('success', 'Data berhasil dipulihkan.');
    }

    public function deletePermanent($id)
    {
        if (session('role') != 'produksi') {
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
        // Get unique SPKs from new production headers (limited to recent)
        $newSpks = $this->headerModel->select('nomor_spk as no_spk, nama_mesin, nama_produksi as nama_produk')
                                    ->orderBy('id', 'DESC')
                                    ->limit(100)
                                    ->findAll();
                                    
        // Get unique SPKs from legacy production table (limited to recent)
        $legacySpks = $this->dbProduksi()->table('prod')
                                  ->select('no_spk, nama_mesin, nama_produk')
                                  ->orderBy('id', 'DESC')
                                  ->limit(100)
                                  ->get()->getResultArray();
                                  
        // Merge and remove duplicates by no_spk
        $merged = array_merge($newSpks, $legacySpks);
        $unique = [];
        foreach ($merged as $item) {
            if (!isset($unique[$item['no_spk']])) {
                $unique[$item['no_spk']] = $item;
            }
        }
        
        return array_values($unique);
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
}
