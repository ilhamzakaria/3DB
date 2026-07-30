<?php

namespace App\Controllers;

use App\Models\MFinishGoodGalon;
use App\Models\MFinishGoodCapGalon;
use App\Models\MFinishGoodFreeform;
use App\Models\MFinishGoodScrewCap;

class FinishGoodPpic extends BaseController
{
    private const TYPE_LABELS = [
        'galon'     => 'Galon',
        'cap_galon' => 'Cap Galon',
        'freeform'  => 'Freeform',
        'screw_cap' => 'Screw Cap',
    ];

    private const TYPE_VIEWS = [
        'galon'     => 'ppic/finish_good_galon',
        'cap_galon' => 'ppic/finish_good_cap_galon',
        'freeform'  => 'ppic/finish_good_freeform',
        'screw_cap' => 'ppic/finish_good_screw_cap',
    ];

    private const TYPE_ROUTES = [
        'galon'     => 'ppic/finish_good/galon',
        'cap_galon' => 'ppic/finish_good/cap_galon',
        'freeform'  => 'ppic/finish_good/freeform',
        'screw_cap' => 'ppic/finish_good/screw_cap',
    ];

    public function __construct()
    {
        helper(['form', 'url']);
    }

    // ----------------------------------------------------------------
    // Index
    // ----------------------------------------------------------------

    public function galon()     { return $this->renderIndex('galon'); }
    public function capGalon()  { return $this->renderIndex('cap_galon'); }
    public function freeform()  { return $this->renderIndex('freeform'); }
    public function screwCap()  { return $this->renderIndex('screw_cap'); }

    // ----------------------------------------------------------------
    // CRUD — Tambah
    // ----------------------------------------------------------------

    public function tambahGalon()     { return $this->doTambah('galon'); }
    public function tambahCapGalon()  { return $this->doTambah('cap_galon'); }
    public function tambahFreeform()  { return $this->doTambah('freeform'); }
    public function tambahScrewCap()  { return $this->doTambah('screw_cap'); }

    // ----------------------------------------------------------------
    // CRUD — Update
    // ----------------------------------------------------------------

    public function updateGalon($id)    { return $this->doUpdate('galon',     (int) $id); }
    public function updateCapGalon($id) { return $this->doUpdate('cap_galon', (int) $id); }
    public function updateFreeform($id) { return $this->doUpdate('freeform',  (int) $id); }
    public function updateScrewCap($id) { return $this->doUpdate('screw_cap', (int) $id); }

    // ----------------------------------------------------------------
    // CRUD — Delete
    // ----------------------------------------------------------------

    public function deleteGalon($id)    { return $this->doDelete('galon',     (int) $id); }
    public function deleteCapGalon($id) { return $this->doDelete('cap_galon', (int) $id); }
    public function deleteFreeform($id) { return $this->doDelete('freeform',  (int) $id); }
    public function deleteScrewCap($id) { return $this->doDelete('screw_cap', (int) $id); }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    private function getModel(string $type)
    {
        return match ($type) {
            'galon'     => new MFinishGoodGalon(),
            'cap_galon' => new MFinishGoodCapGalon(),
            'screw_cap' => new MFinishGoodScrewCap(),
            default     => new MFinishGoodFreeform(),
        };
    }

    private function renderIndex(string $type): string
    {
        $model     = $this->getModel($type);
        $q         = trim((string) $this->request->getGet('q'));
        $startDate = $this->request->getGet('start_date');
        $endDate   = $this->request->getGet('end_date');

        $query = $model;

        if ($q !== '') {
            $query = $query->groupStart()
                ->orLike('bagian', $q)
                ->orLike('nomor', $q)
                ->orLike('nama_produk', $q);

            if ($type === 'galon') {
                $query = $query->orLike('grade', $q)
                    ->orLike('berat', $q)
                    ->orLike('wh_oro', $q)
                    ->orLike('wh_bam', $q);
            } elseif ($type === 'screw_cap') {
                $query = $query->orLike('warna', $q)
                    ->orLike('berat', $q)
                    ->orLike('jumlah', $q)
                    ->orLike('keterangan', $q);
            } else {
                $query = $query->orLike('warna', $q)
                    ->orLike('berat', $q)
                    ->orLike('jumlah', $q)
                    ->orLike('packing', $q);
            }

            $query = $query->groupEnd();
        }

        if (!empty($startDate)) {
            $query = $query->where('tanggal >=', $startDate);
        }
        if (!empty($endDate)) {
            $query = $query->where('tanggal <=', $endDate);
        }

        $data_list = $query->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->paginate(15, 'fg_' . $type);

        // Totals — SUM across ALL filtered rows (not just current page)
        $totals      = ['wh_oro' => 0, 'wh_bam' => 0, 'jumlah' => 0];
        $grandTotals = ['wh_oro' => 0, 'wh_bam' => 0, 'jumlah' => 0];

        if ($type === 'galon') {
            // Filtered total
            $tq = $this->getModel($type);
            if ($q !== '') {
                $tq = $tq->groupStart()
                    ->orLike('bagian', $q)->orLike('nomor', $q)->orLike('nama_produk', $q)
                    ->orLike('grade', $q)->orLike('berat', $q)
                    ->orLike('wh_oro', $q)->orLike('wh_bam', $q)
                    ->groupEnd();
            }
            if (!empty($startDate)) $tq = $tq->where('tanggal >=', $startDate);
            if (!empty($endDate))   $tq = $tq->where('tanggal <=', $endDate);
            $row = $tq->selectSum('wh_oro')->selectSum('wh_bam')->first();
            $totals['wh_oro'] = (int) ($row['wh_oro'] ?? 0);
            $totals['wh_bam'] = (int) ($row['wh_bam'] ?? 0);

            // Grand total — tanpa filter
            $grandRow = $this->getModel($type)->selectSum('wh_oro')->selectSum('wh_bam')->first();
            $grandTotals['wh_oro'] = (int) ($grandRow['wh_oro'] ?? 0);
            $grandTotals['wh_bam'] = (int) ($grandRow['wh_bam'] ?? 0);

        } elseif (in_array($type, ['cap_galon', 'freeform', 'screw_cap'])) {
            // Filtered total — kolom jumlah
            $tq = $this->getModel($type);
            if ($q !== '') {
                $tq = $tq->groupStart()
                    ->orLike('bagian', $q)->orLike('nomor', $q)->orLike('nama_produk', $q)
                    ->orLike('berat', $q)->orLike('jumlah', $q);
                if ($type === 'screw_cap') {
                    $tq = $tq->orLike('warna', $q)->orLike('keterangan', $q);
                } else {
                    $tq = $tq->orLike('warna', $q)->orLike('packing', $q);
                }
                $tq = $tq->groupEnd();
            }
            if (!empty($startDate)) $tq = $tq->where('tanggal >=', $startDate);
            if (!empty($endDate))   $tq = $tq->where('tanggal <=', $endDate);
            $row = $tq->selectSum('jumlah')->first();
            $totals['jumlah'] = (int) ($row['jumlah'] ?? 0);
        }

        return view(self::TYPE_VIEWS[$type], [
            'title'           => 'Finish Good – ' . self::TYPE_LABELS[$type],
            'type'            => $type,
            'type_label'      => self::TYPE_LABELS[$type],
            'data_list'       => $data_list,
            'pager'           => $model->pager,
            'q'               => $q,
            'start_date'      => $startDate,
            'end_date'        => $endDate,
            'current_machine' => 'finish_good_' . $type,
            'modern_layout'   => true,
            'totals'          => $totals,
            'grand_totals'    => $grandTotals,
        ]);
    }

    private function doTambah(string $type): \CodeIgniter\HTTP\RedirectResponse
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url(self::TYPE_ROUTES[$type]));
        }

        $model = $this->getModel($type);
        if ($model->insert($this->buildPostData($type))) {
            session()->setFlashdata('success', 'Data Finish Good ' . self::TYPE_LABELS[$type] . ' berhasil ditambahkan');
        } else {
            session()->setFlashdata('error', 'Gagal menambahkan data');
        }

        return redirect()->to(site_url(self::TYPE_ROUTES[$type]));
    }

    private function doUpdate(string $type, int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        if (!$this->request->is('post')) {
            return redirect()->to(site_url(self::TYPE_ROUTES[$type]));
        }

        $model = $this->getModel($type);
        if ($model->update($id, $this->buildPostData($type))) {
            session()->setFlashdata('success', 'Data Finish Good ' . self::TYPE_LABELS[$type] . ' berhasil diupdate');
        } else {
            session()->setFlashdata('error', 'Gagal mengupdate data');
        }

        return redirect()->to(site_url(self::TYPE_ROUTES[$type]));
    }

    private function doDelete(string $type, int $id): \CodeIgniter\HTTP\RedirectResponse
    {
        $model = $this->getModel($type);
        if ($model->delete($id)) {
            session()->setFlashdata('success', 'Data berhasil dihapus dan dipindahkan ke sampah');
        } else {
            session()->setFlashdata('error', 'Gagal menghapus data');
        }

        return redirect()->to(site_url(self::TYPE_ROUTES[$type]));
    }

    // ================================================================
    // Trash Management (Soft Delete, Restore, Permanent Delete)
    // ================================================================

    public function getTrash()
    {
        // Extract type from URL segment: /ppic/finish_good/{type}/get-trash
        $type = $this->request->getUri()->getSegment(3) ?? 'galon'; // segment 3 = galon|cap_galon|freeform|screw_cap
        
        if (!isset(self::TYPE_LABELS[$type])) {
            $type = 'galon';
        }

        $model = $this->getModel($type);
        $trash = $model->onlyDeleted()
            ->orderBy('deleted_at', 'DESC')
            ->findAll();

        return $this->response->setJSON($trash);
    }

    public function restore(): \CodeIgniter\HTTP\RedirectResponse
    {
        // Extract type and id from URL: /ppic/finish_good/{type}/restore/{id}
        $type = $this->request->getUri()->getSegment(3) ?? 'galon';  // segment 3 = type
        $id   = (int)($this->request->getUri()->getSegment(5) ?? 0); // segment 5 = id
        
        if (!isset(self::TYPE_LABELS[$type]) || $id <= 0) {
            return redirect()->back()->with('error', 'Parameter tidak valid');
        }

        $model = $this->getModel($type);
        
        $model->db->table($model->getTable())->where('id', $id)->update(['deleted_at' => null]);

        return redirect()->back()->with('success', 'Data berhasil dipulihkan dari sampah');
    }

    public function deletePermanent(): \CodeIgniter\HTTP\RedirectResponse
    {
        // Extract type and id from URL: /ppic/finish_good/{type}/delete-permanent/{id}
        $type = $this->request->getUri()->getSegment(3) ?? 'galon';  // segment 3 = type
        $id   = (int)($this->request->getUri()->getSegment(5) ?? 0); // segment 5 = id
        
        if (!isset(self::TYPE_LABELS[$type]) || $id <= 0) {
            return redirect()->back()->with('error', 'Parameter tidak valid');
        }

        $model = $this->getModel($type);
        $model->delete($id, true);  // Force permanent delete

        return redirect()->back()->with('success', 'Data berhasil dihapus permanen');
    }

    private function buildPostData(string $type): array
    {
        $base = [
            'bagian'      => trim((string) $this->request->getPost('bagian')),
            'nomor'       => trim((string) $this->request->getPost('nomor')),
            'nama_produk' => trim((string) $this->request->getPost('nama_produk')),
            'berat'       => trim((string) $this->request->getPost('berat')),
            'tanggal'     => $this->request->getPost('tanggal') ?: date('Y-m-d'),
        ];

        if ($type === 'galon') {
            return array_merge($base, [
                'grade'  => trim((string) $this->request->getPost('grade')),
                'wh_oro' => trim((string) $this->request->getPost('wh_oro')),
                'wh_bam' => trim((string) $this->request->getPost('wh_bam')),
            ]);
        }

        if ($type === 'screw_cap') {
            return array_merge($base, [
                'warna'       => trim((string) $this->request->getPost('warna')),
                'jumlah'      => trim((string) $this->request->getPost('jumlah')),
                'keterangan'  => trim((string) $this->request->getPost('keterangan')),
            ]);
        }

        // cap_galon & freeform
        return array_merge($base, [
            'warna'   => trim((string) $this->request->getPost('warna')),
            'jumlah'  => trim((string) $this->request->getPost('jumlah')),
            'packing' => trim((string) $this->request->getPost('packing')),
        ]);
    }
}
