<?php

namespace App\Controllers;

use App\Models\MBarangMasukPemasok;
use App\Models\MBarangMasukProduksi;
use App\Models\MBarangKeluarPembeli;
use App\Models\MBarangMasukDariProduksi;
use App\Models\MOrowarehouse;
use App\Controllers\Traits\Warehouse2SyncTrait;

class Warehouse2 extends BaseController
{
    use Warehouse2SyncTrait;
    protected $modelPemasok;
    protected $modelProduksi;
    protected $modelPembeli;
    protected $modelMasukProduksi;
    protected $modelOrowarehouse;

    public function __construct()
    {
        $this->modelPemasok = new MBarangMasukPemasok();
        $this->modelProduksi = new MBarangMasukProduksi();
        $this->modelPembeli = new MBarangKeluarPembeli();
        $this->modelMasukProduksi = new MBarangMasukDariProduksi();
        $this->modelOrowarehouse = new MOrowarehouse();
    }

    /**
     * Main page - tampilkan warehouse2 dengan dynamic tab switching
     */
    public function index()
    {
        $data = [
            'title' => 'Warehouse 2',
            'active_tab' => 'pemasok',
            'modern_layout' => true,
            'canEdit' => in_array(session()->get('role'), ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi']),
            'currentUsername' => session()->get('username') ?? 'System'
        ];
        return view('warehouse2/index', $data);
    }

    // ========== BARANG MASUK PEMASOK METHODS ==========

    public function getBarangMasukPemasok()
    {
        try {
            $filters = $this->request->getGet();
            $data = $this->modelPemasok->getWithFilter($filters);

            return $this->response->setJSON([
                'success' => true,
                'status'  => 'success',
                'data'    => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function storePemasok()
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            $data = $this->request->getJSON(true);

            // Manual validation
            $errors = [];

            if (empty($data['nomor_spk'])) {
                $errors['nomor_spk'] = 'Nomor SPK is required';
            }
            if (empty($data['tanggal'])) {
                $errors['tanggal'] = 'Tanggal is required';
            }
            if (empty($data['nama_pemasok'])) {
                $errors['nama_pemasok'] = 'Nama pemasok is required';
            }
            if (empty($data['kode_barang'])) {
                $errors['kode_barang'] = 'Kode barang is required';
            }
            if (empty($data['nama_barang'])) {
                $errors['nama_barang'] = 'Nama barang is required';
            }
            if (empty($data['satuan'])) {
                $errors['satuan'] = 'Satuan is required';
            }
            if (empty($data['jumlah'])) {
                $errors['jumlah'] = 'Jumlah is required';
            }

            // Check for duplicate nomor_spk
            if (!empty($data['nomor_spk'])) {
                $existing = $this->modelPemasok->where('nomor_spk', $data['nomor_spk'])->first();
                if ($existing) {
                    $errors['nomor_spk'] = 'Nomor SPK sudah terdaftar';
                }
            }

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ])->setStatusCode(422);
            }

            $data['status'] = $data['status'] ?? 'diterima';

            $this->modelPemasok->insert($data);

            // Sync warehouse2 nomor_spk to produksi database (if applicable)
            $this->syncWarehouse2ToProduksi($data['nomor_spk'], $data);

            return $this->response->setJSON([
                'success' => true,
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'id' => $this->modelPemasok->insertID()
            ])->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deletePemasok($id)
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            if ($this->modelPemasok->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getBarangMasukPemasokTrash()
    {
        try {
            $data = $this->modelPemasok
                ->where('deleted_at IS NOT NULL', null, false)
                ->orderBy('deleted_at', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getBarangMasukPemasokTrash Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function restorePemasok($id)
    {
        try {
            $data = ['deleted_at' => null];
            if ($this->modelPemasok->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dipulihkan'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'restorePemasok Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deletePermanentPemasok($id)
    {
        try {
            if ($this->modelPemasok->delete($id, true)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dihapus permanen'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'deletePermanentPemasok Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function exportPemasok()
    {
        try {
            $query = $this->modelPemasok->where('deleted_at IS NULL', null, false);

            // Date range filter
            if (!empty($this->request->getGet('tanggal_dari')) && !empty($this->request->getGet('tanggal_sampai'))) {
                $query->where('DATE(tanggal) >=', $this->request->getGet('tanggal_dari'))
                    ->where('DATE(tanggal) <=', $this->request->getGet('tanggal_sampai'));
            }

            // Status filter
            if (!empty($this->request->getGet('status'))) {
                $query->where('status', $this->request->getGet('status'));
            }

            // Pemasok filter
            if (!empty($this->request->getGet('nama_pemasok'))) {
                $query->like('nama_pemasok', $this->request->getGet('nama_pemasok'));
            }

            // Search
            if (!empty($this->request->getGet('search'))) {
                $search = $this->request->getGet('search');
                $query->groupStart()
                    ->like('no_dokumen', $search)
                    ->orLike('nama_barang', $search)
                    ->orLike('nama_pemasok', $search)
                    ->groupEnd();
            }

            $data = $query->orderBy('tanggal', 'DESC')->findAll();

            $headers = [
                'Nomor SPK',
                'Tanggal',
                'Pemasok',
                'Barang',
                'Satuan',
                'Jumlah',
                'Harga Satuan',
                'Total Harga',
                'No PO',
                'No Surat Jalan',
                'Status',
                'Catatan'
            ];

            $exportData = [];
            if (!empty($data)) {
                foreach ($data as $row) {
                    $exportData[] = [
                        $row['nomor_spk'] ?? '',
                        $row['tanggal'] ?? '',
                        $row['nama_pemasok'] ?? '',
                        $row['nama_barang'] ?? '',
                        $row['satuan'] ?? '',
                        $row['jumlah'] ?? 0,
                        $row['harga_satuan'] ?? 0,
                        $row['total_harga'] ?? 0,
                        $row['no_po'] ?? '',
                        $row['no_surat_jalan'] ?? '',
                        $row['status'] ?? '',
                        $row['catatan'] ?? '',
                    ];
                }
            }

            $filename = 'barang-masuk-pemasok-' . date('Y-m-d-His');
            $excel = new \App\Libraries\ExcelExport();
            return $excel->export($filename, $headers, $exportData);
        } catch (\Exception $e) {
            log_message('error', 'exportPemasok Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengexport data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // ========== BARANG MASUK PRODUKSI METHODS ==========

    public function getBarangMasukProduksi()
    {
        try {
            $filters = $this->request->getGet();
            $query = $this->modelProduksi->where('deleted_at IS NULL', null, false);

            // Status filter
            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            // Date range filter
            if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
                $query->where('DATE(tanggal) >=', $filters['tanggal_dari'])
                    ->where('DATE(tanggal) <=', $filters['tanggal_sampai']);
            }

            // Search filter (including by ID for edit)
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->groupStart()
                    ->where('id', $search)
                    ->orLike('nomor_spk', $search)
                    ->orLike('nama_barang', $search)
                    ->groupEnd();
            }

            $data = $query->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC')->findAll();

            return $this->response->setJSON([
                'success' => true,
                'status' => 'success',
                'data' => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getBarangMasukProduksi Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function storeProduksi()
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            $data = $this->request->getJSON(true);

            // Manual validation
            $errors = [];

            if (empty($data['nomor_spk'])) {
                $errors['nomor_spk'] = 'Nomor SPK is required';
            }
            if (empty($data['tanggal'])) {
                $errors['tanggal'] = 'Tanggal is required';
            }
            if (empty($data['kode_barang'])) {
                $errors['kode_barang'] = 'Kode barang is required';
            }
            if (empty($data['nama_barang'])) {
                $errors['nama_barang'] = 'Nama barang is required';
            }
            if (empty($data['satuan'])) {
                $errors['satuan'] = 'Satuan is required';
            }
            if (empty($data['jumlah_keluar'])) {
                $errors['jumlah_keluar'] = 'Jumlah keluar is required';
            }

            // Check for duplicate nomor_spk
            if (!empty($data['nomor_spk'])) {
                $existing = $this->modelProduksi->where('nomor_spk', $data['nomor_spk'])->first();
                if ($existing) {
                    $errors['nomor_spk'] = 'Nomor SPK sudah terdaftar';
                }
            }

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ])->setStatusCode(422);
            }

            $data['status'] = $data['status'] ?? 'diterima';

            $this->modelProduksi->insert($data);

            // Sync warehouse2 nomor_spk to produksi database
            $this->syncBarangMasukProduksiToDb($data['nomor_spk'], $data);

            return $this->response->setJSON([
                'success' => true,
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'id' => $this->modelProduksi->insertID()
            ])->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function updateProduksi($id)
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            $data = $this->request->getJSON(true);

            // Manual validation
            $errors = [];

            if (empty($data['nomor_spk'])) {
                $errors['nomor_spk'] = 'Nomor SPK is required';
            }
            if (empty($data['tanggal'])) {
                $errors['tanggal'] = 'Tanggal is required';
            }
            if (empty($data['kode_barang'])) {
                $errors['kode_barang'] = 'Kode barang is required';
            }
            if (empty($data['nama_barang'])) {
                $errors['nama_barang'] = 'Nama barang is required';
            }
            if (empty($data['satuan'])) {
                $errors['satuan'] = 'Satuan is required';
            }
            if (empty($data['jumlah_keluar'])) {
                $errors['jumlah_keluar'] = 'Jumlah keluar is required';
            }

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ])->setStatusCode(422);
            }

            // Get existing record
            $existing = $this->modelProduksi->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Remove ID from update data
            unset($data['id']);

            // CI4 update() returns false when 0 rows are affected (data unchanged),
            // not just on real errors — so we don't gate success on its return value.
            $this->modelProduksi->update($id, $data);

            // Check for model-level DB errors
            $dbError = $this->modelProduksi->errors();
            if (!empty($dbError)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Gagal menyimpan: ' . implode(', ', $dbError)
                ])->setStatusCode(500);
            }

            // Sync warehouse2 nomor_spk to produksi database
            $this->syncBarangMasukProduksiToDb($data['nomor_spk'], $data);

            return $this->response->setJSON([
                'success' => true,
                'status' => 'success',
                'message' => 'Data berhasil diubah'
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deleteProduksi($id)
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            if ($this->modelProduksi->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getBarangMasukProduksiTrash()
    {
        try {
            $data = $this->modelProduksi
                ->where('deleted_at IS NOT NULL', null, false)
                ->orderBy('deleted_at', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getBarangMasukProduksiTrash Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function restoreProduksi($id)
    {
        try {
            $data = ['deleted_at' => null];
            if ($this->modelProduksi->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dipulihkan'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'restoreProduksi Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deletePermanentProduksi($id)
    {
        try {
            if ($this->modelProduksi->delete($id, true)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dihapus permanen'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'deletePermanentProduksi Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function exportProduksi()
    {
        try {
            $query = $this->modelProduksi->where('deleted_at IS NULL', null, false);

            // Date range filter
            if (!empty($this->request->getGet('tanggal_dari')) && !empty($this->request->getGet('tanggal_sampai'))) {
                $query->where('DATE(tanggal) >=', $this->request->getGet('tanggal_dari'))
                    ->where('DATE(tanggal) <=', $this->request->getGet('tanggal_sampai'));
            }

            // Status filter
            if (!empty($this->request->getGet('status'))) {
                $query->where('status', $this->request->getGet('status'));
            }

            // SPK filter
            if (!empty($this->request->getGet('spk'))) {
                $query->like('spk', $this->request->getGet('spk'));
            }

            // Search
            if (!empty($this->request->getGet('search'))) {
                $search = $this->request->getGet('search');
                $query->groupStart()
                    ->like('no_dokumen', $search)
                    ->orLike('nama_barang', $search)
                    ->orLike('no_spk', $search)
                    ->groupEnd();
            }

            $data = $query->orderBy('tanggal', 'DESC')->findAll();

            $headers = [
                'Nomor SPK',
                'Tanggal',
                'No SPK',
                'Produksi',
                'Barang',
                'Satuan',
                'Keluar',
                'Terima',
                'Status',
                'Catatan'
            ];

            $exportData = [];
            if (!empty($data)) {
                foreach ($data as $row) {
                    $exportData[] = [
                        $row['nomor_spk'] ?? '',
                        $row['tanggal'] ?? '',
                        $row['no_spk'] ?? '',
                        $row['nama_produksi'] ?? '',
                        $row['nama_barang'] ?? '',
                        $row['satuan'] ?? '',
                        $row['jumlah_keluar'] ?? 0,
                        $row['jumlah_terima'] ?? 0,
                        $row['status'] ?? '',
                        $row['catatan'] ?? '',
                    ];
                }
            }

            $filename = 'barang-masuk-produksi-' . date('Y-m-d-His');
            $excel = new \App\Libraries\ExcelExport();
            return $excel->export($filename, $headers, $exportData);
        } catch (\Exception $e) {
            log_message('error', 'exportProduksi Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengexport data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // ========== BARANG KELUAR PEMBELI METHODS ==========

    public function getBarangKeluarPembeli()
    {
        try {
            $filters  = $this->request->getGet();
            $perPage  = (int)($this->request->getGet('per_page') ?? 10);
            $page     = (int)($this->request->getGet('page') ?? 1);

            $query = $this->modelPembeli->where('deleted_at IS NULL', null, false);

            if (!empty($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (!empty($filters['tanggal_dari'])) {
                $query->where('DATE(tanggal) >=', $filters['tanggal_dari']);
            }
            if (!empty($filters['tanggal_sampai'])) {
                $query->where('DATE(tanggal) <=', $filters['tanggal_sampai']);
            }
            if (!empty($filters['nama_pembeli'])) {
                $query->like('nama_pembeli', $filters['nama_pembeli']);
            }
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query->groupStart()
                    ->like('nomor_spk', $search)
                    ->orLike('nama_pembeli', $search)
                    ->orLike('nama_barang', $search)
                    ->groupEnd();
            }

            $query->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC');

            $pager = service('pager');
            $data  = $query->paginate($perPage, 'default', $page);
            $pagerInfo = $pager->getDetails();
            $pagerInfo = is_array($pagerInfo) ? $pagerInfo : [];

            return $this->response->setJSON([
                'success' => true,
                'data'    => $data,
                'pager'   => [
                    'current_page' => (int)($pagerInfo['currentPage'] ?? $pagerInfo['current_page'] ?? 1),
                    'total_pages'  => (int)($pagerInfo['pageCount'] ?? $pagerInfo['totalPages'] ?? $pagerInfo['page_count'] ?? 1),
                    'per_page'     => (int)($pagerInfo['perPage'] ?? $pagerInfo['per_page'] ?? $perPage),
                    'total'        => (int)($pagerInfo['total'] ?? 0),
                    'has_prev'     => (bool)($pagerInfo['hasPrevious'] ?? $pagerInfo['hasPreviousPage'] ?? $pagerInfo['has_previous'] ?? false),
                    'has_next'     => (bool)($pagerInfo['hasNext'] ?? $pagerInfo['hasNextPage'] ?? $pagerInfo['has_next'] ?? false),
                ],
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function storePembeli()
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            $data = $this->request->getJSON(true);

            // Manual validation
            $errors = [];

            if (empty($data['nomor_spk'])) {
                $errors['nomor_spk'] = 'Nomor SPK is required';
            }
            if (empty($data['tanggal'])) {
                $errors['tanggal'] = 'Tanggal is required';
            }
            if (empty($data['nama_pembeli'])) {
                $errors['nama_pembeli'] = 'Nama pembeli is required';
            }
            if (empty($data['kode_barang'])) {
                $errors['kode_barang'] = 'Kode barang is required';
            }
            if (empty($data['nama_barang'])) {
                $errors['nama_barang'] = 'Nama barang is required';
            }
            if (empty($data['satuan'])) {
                $errors['satuan'] = 'Satuan is required';
            }
            if (empty($data['jumlah'])) {
                $errors['jumlah'] = 'Jumlah is required';
            }


            if (!empty($errors)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ])->setStatusCode(422);
            }

            $data['harga_satuan'] = $data['harga_satuan'] ?? 0;
            // Calculate total_harga
            $data['total_harga'] = $data['jumlah'] * $data['harga_satuan'];
            $data['status'] = $data['status'] ?? 'dikirim';

            error_log("DEBUG: savePembeli - data before insert: " . json_encode($data));

            $this->modelPembeli->insert($data);
            $pembeliId = $this->modelPembeli->insertID();

            error_log("DEBUG: Calling deductGrandTotalBagusFromProduksi with nomor_spk='{$data['nomor_spk']}', jumlah='{$data['jumlah']}'");
            // Deduct dari grand_total_bagus di database produksi
            $this->deductGrandTotalBagusFromProduksi($data['nomor_spk'], $data['jumlah']);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'id' => $pembeliId
            ])->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deletePembeli($id)
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            // Get the record before deletion to restore inventory
            $record = $this->modelPembeli->find($id);
            if (!$record) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }

            if ($this->modelPembeli->delete($id)) {
                // Restore inventory: add back the jumlah to grand_total_bagus
                $this->restoreInventoryForDeletedPembeli($record['nomor_spk'], $record['jumlah']);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function updatePembeli($id)
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            $data = $this->request->getJSON(true);

            // Manual validation
            $errors = [];

            if (empty($data['nomor_spk'])) {
                $errors['nomor_spk'] = 'Nomor SPK is required';
            }
            if (empty($data['tanggal'])) {
                $errors['tanggal'] = 'Tanggal is required';
            }
            if (empty($data['nama_pembeli'])) {
                $errors['nama_pembeli'] = 'Nama pembeli is required';
            }
            if (empty($data['kode_barang'])) {
                $errors['kode_barang'] = 'Kode barang is required';
            }
            if (empty($data['nama_barang'])) {
                $errors['nama_barang'] = 'Nama barang is required';
            }
            if (empty($data['satuan'])) {
                $errors['satuan'] = 'Satuan is required';
            }
            if (empty($data['jumlah'])) {
                $errors['jumlah'] = 'Jumlah is required';
            }

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ])->setStatusCode(422);
            }

            // Get existing record to calculate inventory adjustment
            $existing = $this->modelPembeli->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }

            // Calculate total_harga
            $data['harga_satuan'] = $data['harga_satuan'] ?? 0;
            $data['total_harga'] = $data['jumlah'] * $data['harga_satuan'];

            // Remove ID from update data
            unset($data['id']);

            if ($this->modelPembeli->update($id, $data)) {
                // Handle inventory adjustment if jumlah changed
                $oldJumlah = (int)$existing['jumlah'];
                $newJumlah = (int)$data['jumlah'];
                $jumlahDifference = $newJumlah - $oldJumlah;

                // If jumlah increased, deduct more from inventory
                // If jumlah decreased, add back to inventory
                if ($jumlahDifference != 0) {
                    $this->adjustInventoryForPembeli($data['nomor_spk'], $jumlahDifference);
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil diubah'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getBarangKeluarPembeliTrash()
    {
        try {
            $data = $this->modelPembeli
                ->where('deleted_at IS NOT NULL', null, false)
                ->orderBy('deleted_at', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getBarangKeluarPembeliTrash Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function restorePembeli($id)
    {
        try {
            $data = ['deleted_at' => null];
            if ($this->modelPembeli->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dipulihkan'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'restorePembeli Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deletePermanentPembeli($id)
    {
        try {
            if ($this->modelPembeli->delete($id, true)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dihapus permanen'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'deletePermanentPembeli Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function exportPembeli()
    {
        try {
            $query = $this->modelPembeli->where('deleted_at IS NULL', null, false);

            // Date range filter
            if (!empty($this->request->getGet('tanggal_dari')) && !empty($this->request->getGet('tanggal_sampai'))) {
                $query->where('DATE(tanggal) >=', $this->request->getGet('tanggal_dari'))
                    ->where('DATE(tanggal) <=', $this->request->getGet('tanggal_sampai'));
            }

            // Status filter
            if (!empty($this->request->getGet('status'))) {
                $query->where('status', $this->request->getGet('status'));
            }

            // Pembeli filter
            if (!empty($this->request->getGet('nama_pembeli'))) {
                $query->like('nama_pembeli', $this->request->getGet('nama_pembeli'));
            }

            // Search
            if (!empty($this->request->getGet('search'))) {
                $search = $this->request->getGet('search');
                $query->groupStart()
                    ->like('no_dokumen', $search)
                    ->orLike('nama_barang', $search)
                    ->orLike('nama_pembeli', $search)
                    ->groupEnd();
            }

            $data = $query->orderBy('tanggal', 'DESC')->findAll();

            $headers = [
                'Nomor SPK',
                'Tanggal',
                'Pembeli',
                'Barang',
                'Satuan',
                'Jumlah',
                'Harga Satuan',
                'Total Harga',
                'No SO',
                'No Invoice',
                'Status',
                'Catatan'
            ];

            $exportData = [];
            if (!empty($data)) {
                foreach ($data as $row) {
                    $exportData[] = [
                        $row['nomor_spk'] ?? '',
                        $row['tanggal'] ?? '',
                        $row['nama_pembeli'] ?? '',
                        $row['nama_barang'] ?? '',
                        $row['satuan'] ?? '',
                        $row['jumlah'] ?? 0,
                        $row['harga_satuan'] ?? 0,
                        $row['total_harga'] ?? 0,
                        $row['no_so'] ?? '',
                        $row['no_invoice'] ?? '',
                        $row['status'] ?? '',
                        $row['catatan'] ?? '',
                    ];
                }
            }

            $filename = 'barang-keluar-pembeli-' . date('Y-m-d-His');
            $excel = new \App\Libraries\ExcelExport();
            return $excel->export($filename, $headers, $exportData);
        } catch (\Exception $e) {
            log_message('error', 'exportPembeli Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengexport data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // ========== BARANG MASUK DARI PRODUKSI METHODS ==========

    public function getBarangMasukDariProduksi()
    {
        try {
            $filters = $this->request->getGet();
            $data = $this->modelMasukProduksi->getWithFilter($filters);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function storeMasukProduksi()
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            $data = $this->request->getJSON(true);

            // Manual validation
            $errors = [];

            if (empty($data['nomor_spk'])) {
                $errors['nomor_spk'] = 'Nomor SPK is required';
            }
            if (empty($data['tanggal'])) {
                $errors['tanggal'] = 'Tanggal is required';
            }
            if (empty($data['no_spk'])) {
                $errors['no_spk'] = 'No SPK is required';
            }
            if (empty($data['nama_produksi'])) {
                $errors['nama_produksi'] = 'Nama produksi is required';
            }
            if (empty($data['kode_barang'])) {
                $errors['kode_barang'] = 'Kode barang is required';
            }
            if (empty($data['nama_barang'])) {
                $errors['nama_barang'] = 'Nama barang is required';
            }
            if (empty($data['satuan'])) {
                $errors['satuan'] = 'Satuan is required';
            }
            if (empty($data['jumlah_produksi'])) {
                $errors['jumlah_produksi'] = 'Jumlah produksi is required';
            }

            // Check for duplicate nomor_spk
            if (!empty($data['nomor_spk'])) {
                $existing = $this->modelMasukProduksi->where('nomor_spk', $data['nomor_spk'])->first();
                if ($existing) {
                    $errors['nomor_spk'] = 'Nomor SPK sudah terdaftar';
                }
            }

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $errors
                ])->setStatusCode(422);
            }

            $data['status'] = $data['status'] ?? 'diterima';

            $this->modelMasukProduksi->insert($data);

            // Sync warehouse2 nomor_spk to produksi database
            $this->syncWarehouse2ToProduksi($data['nomor_spk'], $data);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Data berhasil disimpan',
                'id' => $this->modelMasukProduksi->insertID()
            ])->setStatusCode(201);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deleteMasukProduksi($id)
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            if ($this->modelMasukProduksi->delete($id)) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Data berhasil dihapus'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    // ========== OROWAREHOUSE SECTION ==========

    public function getOrowarehouse()
    {
        try {
            $filters = $this->request->getGet();
            $perPage = (int)($this->request->getGet('per_page') ?? 10);
            $page = (int)($this->request->getGet('page') ?? 1);

            // Build query with filters
            $query = $this->modelOrowarehouse->where('deleted_at IS NULL', null, false);

            // Period filter
            $period = $this->request->getGet('period');
            if ($period === 'day') {
                $query->where('DATE(tanggal)', date('Y-m-d'));
            } elseif ($period === 'week') {
                $query->where('tanggal >=', date('Y-m-d', strtotime('-7 days')));
            } elseif ($period === 'month') {
                $query->where('tanggal >=', date('Y-m-d', strtotime('-30 days')));
            }

            // Status filter
            if (!empty($filters['status'])) {
                $query = $query->where('status', $filters['status']);
            }

            // Date filter
            if (!empty($filters['tanggal'])) {
                $query->where('DATE(tanggal)', $filters['tanggal']);
            }

            // SPK filter
            if (!empty($filters['spk'])) {
                $query = $query->like('spk', $filters['spk']);
            }

            // Produk filter
            if (!empty($filters['produk'])) {
                $query = $query->like('produk', $filters['produk']);
            }

            // Shift filter
            if (!empty($filters['shift'])) {
                $query = $query->where('shift', $filters['shift']);
            }

            // Grup filter
            if (!empty($filters['grup'])) {
                $query = $query->like('grup', $filters['grup']);
            }

            // Operator filter
            if (!empty($filters['operator'])) {
                $query = $query->like('operator', $filters['operator']);
            }

            // Search - mencari di multiple fields
            if (!empty($filters['search'])) {
                $search = $filters['search'];
                $query = $query->groupStart()
                    ->like('spk', $search)
                    ->orLike('produk', $search)
                    ->orLike('operator', $search)
                    ->orLike('material', $search)
                    ->groupEnd();
            }

            // Order by
            $query = $query->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC');

            // Use CodeIgniter's paginate method
            $pager = service('pager');
            $data = $query->paginate($perPage, 'default', $page);
            $pagerInfo = $pager->getDetails();
            $pagerInfo = is_array($pagerInfo) ? $pagerInfo : [];

            // Calculate overall summary totals (deducting barang_keluar_pembeli from total_bagus)
            $sumBagusRow = $this->modelOrowarehouse->where('deleted_at IS NULL', null, false)->selectSum('hasil_bagus')->first();
            $totalBagusGross = (float)($sumBagusRow['hasil_bagus'] ?? 0);

            $sumRejectRow = $this->modelOrowarehouse->where('deleted_at IS NULL', null, false)->selectSum('hasil_reject')->first();
            $totalReject = (float)($sumRejectRow['hasil_reject'] ?? 0);

            $sumPembeliRow = $this->modelPembeli->where('deleted_at IS NULL', null, false)->selectSum('jumlah')->first();
            $totalKeluarPembeli = (float)($sumPembeliRow['jumlah'] ?? 0);

            $totalBagusNet = max(0, $totalBagusGross - $totalKeluarPembeli);

            return $this->response->setJSON([
                'success' => true,
                'data' => $data,
                'summary' => [
                    'total_bagus' => $totalBagusNet,
                    'total_bagus_gross' => $totalBagusGross,
                    'total_keluar_pembeli' => $totalKeluarPembeli,
                    'total_reject' => $totalReject,
                ],
                'pager' => [
                    'current_page' => (int)($pagerInfo['currentPage'] ?? $pagerInfo['current_page'] ?? 1),
                    'total_pages' => (int)($pagerInfo['pageCount'] ?? $pagerInfo['totalPages'] ?? $pagerInfo['page_count'] ?? 1),
                    'per_page' => (int)($pagerInfo['perPage'] ?? $pagerInfo['per_page'] ?? $perPage),
                    'total' => (int)($pagerInfo['total'] ?? 0),
                    'has_prev' => (bool)($pagerInfo['hasPrevious'] ?? $pagerInfo['hasPreviousPage'] ?? $pagerInfo['has_previous'] ?? false),
                    'has_next' => (bool)($pagerInfo['hasNext'] ?? $pagerInfo['hasNextPage'] ?? $pagerInfo['has_next'] ?? false)
                ]
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getOrowarehouse Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getSpkList()
    {
        try {
            // Query from produksi database to get SPK list
            $db = \Config\Database::connect('produksi');

            $spks = $db->table('produksi_headers')
                ->select('nomor_spk as spk, nama_produk as produk, tanggal')
                ->where('deleted_at', null)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(100)
                ->get()
                ->getResultArray();

            // Remove duplicates (keep only latest SPK)
            $seen = [];
            $unique = [];
            foreach ($spks as $row) {
                if (!isset($seen[$row['spk']])) {
                    $seen[$row['spk']] = true;
                    $unique[] = $row;
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $unique
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getSpkList Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getOrowarehouseBySpk()
    {
        try {
            $spk = $this->request->getGet('spk');

            if (!$spk) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'SPK harus diisi'
                ])->setStatusCode(400);
            }

            // Query from produksi database
            $db = \Config\Database::connect('produksi');

            // Get header data
            $header = $db->table('produksi_headers')
                ->select('id, nomor_spk, nama_produk, shift, grup, operator, packing, tanggal, grand_total_bagus, grand_total_reject')
                ->where('nomor_spk', $spk)
                ->where('deleted_at', null)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            if (!$header) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data SPK tidak ditemukan di produksi'
                ])->setStatusCode(404);
            }

            $headerId = $header['id'];

            // Get materials
            $materials = $db->table('produksi_materials')
                ->select('merek_kode')
                ->where('produksi_header_id', $headerId)
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();
            $materialStr = !empty($materials) ? implode(', ', array_column($materials, 'merek_kode')) : '';

            // Get colorants
            $colorants = $db->table('produksi_colorants')
                ->select('code')
                ->where('produksi_header_id', $headerId)
                ->where('deleted_at', null)
                ->get()
                ->getResultArray();
            $colorantStr = !empty($colorants) ? implode(', ', array_column($colorants, 'code')) : '';

            // Get packaging
            $packaging = $db->table('produksi_packagings')
                ->select('box, box_ukuran, plastik, plastik_ukuran, karung, karung_ukuran')
                ->where('produksi_header_id', $headerId)
                ->where('deleted_at', null)
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            $packagingStr = '';
            if (!empty($packaging)) {
                $parts = [];
                if (!empty($packaging['box']) && $packaging['box'] > 0) {
                    $parts[] = 'Box' . (!empty($packaging['box_ukuran']) ? ' ' . $packaging['box_ukuran'] : '');
                }
                if (!empty($packaging['plastik']) && $packaging['plastik'] > 0) {
                    $parts[] = 'Plastik' . (!empty($packaging['plastik_ukuran']) ? ' ' . $packaging['plastik_ukuran'] : '');
                }
                if (!empty($packaging['karung']) && $packaging['karung'] > 0) {
                    $parts[] = 'Karung' . (!empty($packaging['karung_ukuran']) ? ' ' . $packaging['karung_ukuran'] : '');
                }
                $packagingStr = implode(', ', $parts);
            }

            if (empty($packagingStr) && !empty($header['packing'])) {
                $packagingStr = $header['packing'];
            }

            $data = [
                'spk'           => $header['nomor_spk'],
                'produk'        => $header['nama_produk'],
                'shift'         => (string) $header['shift'],
                'grup'          => $header['grup'],
                'operator'      => $header['operator'],
                'material'      => $materialStr,
                'colorant'      => $colorantStr,
                'packaging'     => $packagingStr,
                'tanggal'       => $header['tanggal'],
                'hasil_bagus'   => $header['grand_total_bagus'],
                'hasil_reject'  => $header['grand_total_reject'],
            ];

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getOrowarehouseBySpk Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function getOrowarehouseFilters()
    {
        try {
            // Get unique values from model
            $shiftsRaw = $this->modelOrowarehouse->getUniqueShifts();
            $grupsRaw = $this->modelOrowarehouse->getUniqueGrups();
            $operatorsRaw = $this->modelOrowarehouse->getUniqueOperators();

            // Extract just the values we need
            $shifts = array_map(function ($item) {
                return $item['shift'] ?? null;
            }, $shiftsRaw);
            $shifts = array_filter($shifts); // Remove null values
            $shifts = array_values($shifts); // Re-index array

            $grups = array_map(function ($item) {
                return $item['grup'] ?? null;
            }, $grupsRaw);
            $grups = array_filter($grups);
            $grups = array_values($grups);

            $operators = array_map(function ($item) {
                return $item['operator'] ?? null;
            }, $operatorsRaw);
            $operators = array_filter($operators);
            $operators = array_values($operators);

            return $this->response->setJSON([
                'success' => true,
                'shifts' => $shifts,
                'grups' => $grups,
                'operators' => $operators
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getOrowarehouseFilters Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function storeOrowarehouse()
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            $data = $this->request->getJSON(true);

            // Manual validation
            $errors = [];

            if (empty($data['tanggal'])) {
                $errors['tanggal'] = 'Tanggal is required';
            }
            if (empty($data['spk'])) {
                $errors['spk'] = 'SPK is required';
            }
            if (empty($data['produk'])) {
                $errors['produk'] = 'Produk is required';
            }
            if (empty($data['shift'])) {
                $errors['shift'] = 'Shift is required';
            }
            if (empty($data['hasil_bagus']) && empty($data['hasil_reject'])) {
                $errors['hasil'] = 'Hasil bagus atau hasil reject harus diisi';
            }

            if (!empty($errors)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $errors
                ])->setStatusCode(422);
            }

            $data['status'] = $data['status'] ?? 'pending';

            if ($this->modelOrowarehouse->insert($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil disimpan',
                    'id' => $this->modelOrowarehouse->insertID()
                ])->setStatusCode(201);
            }

            throw new \Exception('Failed to insert data');
        } catch (\Exception $e) {
            log_message('error', 'storeOrowarehouse Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function updateOrowarehouse($id)
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            $data = $this->request->getJSON(true);

            $existing = $this->modelOrowarehouse->find($id);
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Data tidak ditemukan'
                ])->setStatusCode(404);
            }

            if ($this->modelOrowarehouse->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil diperbarui'
                ])->setStatusCode(200);
            }

            throw new \Exception('Failed to update data');
        } catch (\Exception $e) {
            log_message('error', 'updateOrowarehouse Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    public function deleteOrowarehouse($id)
    {
        try {
            // Security: Only admin, manager, manager_warehouse2, operator_produksi can write
            $role = session()->get('role');
            if (!in_array($role, ['gudang', 'admin', 'manager', 'manager_warehouse2', 'operator_produksi', 'warehouse2'])) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Anda tidak memiliki izin untuk melakukan aksi ini.'
                ])->setStatusCode(403);
            }

            // Use Model::delete() to perform a proper soft-delete (sets deleted_at)
            if ($this->modelOrowarehouse->delete($id)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dipindahkan ke sampah'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'deleteOrowarehouse Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get Orowarehouse Trash Data
     */
    public function getOrowarehouseTrash()
    {
        try {
            $data = $this->modelOrowarehouse
                ->where('deleted_at IS NOT NULL', null, false)
                ->orderBy('deleted_at', 'DESC')
                ->findAll();

            return $this->response->setJSON([
                'success' => true,
                'data' => $data
            ])->setStatusCode(200);
        } catch (\Exception $e) {
            log_message('error', 'getOrowarehouseTrash Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Restore Orowarehouse from Trash
     */
    public function restoreOrowarehouse($id)
    {
        try {
            $data = ['deleted_at' => null];
            if ($this->modelOrowarehouse->update($id, $data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dipulihkan'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'restoreOrowarehouse Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Delete Orowarehouse Permanently
     */
    public function deletePermanentOrowarehouse($id)
    {
        try {
            if ($this->modelOrowarehouse->delete($id, true)) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Data berhasil dihapus permanen'
                ])->setStatusCode(200);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ])->setStatusCode(404);
        } catch (\Exception $e) {
            log_message('error', 'deletePermanentOrowarehouse Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Export Orowarehouse to Excel
     */
    public function exportOrowarehouse()
    {
        try {
            $query = $this->modelOrowarehouse->where('deleted_at IS NULL', null, false);

            // Time-based filtering (Period filter)
            $period = $this->request->getGet('period');
            if ($period === 'day') {
                $query->where('DATE(tanggal)', date('Y-m-d'));
            } elseif ($period === 'week') {
                $query->where('tanggal >=', date('Y-m-d', strtotime('-7 days')));
            } elseif ($period === 'month') {
                $query->where('tanggal >=', date('Y-m-d', strtotime('-30 days')));
            }

            // Custom filters
            if (!empty($this->request->getGet('tanggal'))) {
                $query->where('DATE(tanggal)', $this->request->getGet('tanggal'));
            }

            if (!empty($this->request->getGet('shift'))) {
                $query->where('shift', $this->request->getGet('shift'));
            }

            if (!empty($this->request->getGet('status'))) {
                $query->where('status', $this->request->getGet('status'));
            }

            if (!empty($this->request->getGet('grup'))) {
                $query->like('grup', $this->request->getGet('grup'));
            }

            if (!empty($this->request->getGet('operator'))) {
                $query->like('operator', $this->request->getGet('operator'));
            }

            // Search
            if (!empty($this->request->getGet('search'))) {
                $search = $this->request->getGet('search');
                $query->groupStart()
                    ->like('spk', $search)
                    ->orLike('produk', $search)
                    ->orLike('operator', $search)
                    ->orLike('material', $search)
                    ->groupEnd();
            }

            $data = $query->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->findAll();

            if (empty($data)) {
                // Return empty template
                $headers = [
                    'Tanggal',
                    'SPK',
                    'Produk',
                    'Shift',
                    'Grup',
                    'Hasil Bagus',
                    'Hasil Reject',
                    'Material',
                    'Colorant',
                    'Packaging',
                    'Operator',
                    'Status',
                    'Catatan'
                ];
                $filename = 'orowarehouse-' . date('Y-m-d-His');
                $excel = new \App\Libraries\ExcelExport();
                return $excel->export($filename, $headers, []);
            }

            $headers = [
                'Tanggal',
                'SPK',
                'Produk',
                'Shift',
                'Grup',
                'Hasil Bagus',
                'Hasil Reject',
                'Material',
                'Colorant',
                'Packaging',
                'Operator',
                'Status',
                'Catatan'
            ];

            $exportData = [];
            foreach ($data as $row) {
                $exportData[] = [
                    $row['tanggal'] ?? '',
                    $row['spk'] ?? '',
                    $row['produk'] ?? '',
                    $row['shift'] ?? '',
                    $row['grup'] ?? '',
                    $row['hasil_bagus'] ?? 0,
                    $row['hasil_reject'] ?? 0,
                    $row['material'] ?? '',
                    $row['colorant'] ?? '',
                    $row['packaging'] ?? '',
                    $row['operator'] ?? '',
                    $row['status'] ?? '',
                    $row['catatan'] ?? '',
                ];
            }

            $filename = 'orowarehouse-' . date('Y-m-d-His');
            $excel = new \App\Libraries\ExcelExport();
            return $excel->export($filename, $headers, $exportData);
        } catch (\Exception $e) {
            log_message('error', 'exportOrowarehouse Error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Gagal mengexport data: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Restore inventory when pembeli record is deleted
     * 
     * @param string $noSpk - Nomor SPK
     * @param float $jumlah - Jumlah yang harus di-restore
     */
    private function restoreInventoryForDeletedPembeli($noSpk, $jumlah)
    {
        try {
            // Connect to produksi database
            $db = \Config\Database::connect('produksi');

            // Find the produksi_headers record
            $header = $db->table('produksi_headers')
                ->select('id, grand_total_bagus')
                ->where('nomor_spk', $noSpk)
                ->where('deleted_at', null)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            if (!$header) {
                log_message('warning', "SPK '{$noSpk}' not found in produksi database for inventory restoration");
                return;
            }

            // Add back the jumlah to grand_total_bagus
            $newBagus = (int)$header['grand_total_bagus'] + (int)$jumlah;

            // Update the produksi_headers record
            $db->table('produksi_headers')
                ->where('id', $header['id'])
                ->update(['grand_total_bagus' => $newBagus]);

            log_message('info', "Restored {$jumlah} to SPK '{$noSpk}' grand_total_bagus after deletion. New value: {$newBagus}");
        } catch (\Exception $e) {
            // Log the error but don't throw exception
            log_message('error', "Error restoring inventory for deleted SPK '{$noSpk}': " . $e->getMessage());
        }
    }

    /**
     * Adjust inventory when pembeli record jumlah is updated
     * 
     * @param string $noSpk - Nomor SPK
     * @param float $jumlahDifference - Selisih jumlah baru dikurang jumlah lama
     */
    private function adjustInventoryForPembeli($noSpk, $jumlahDifference)
    {
        if ($jumlahDifference > 0) {
            $this->deductGrandTotalBagusFromProduksi($noSpk, $jumlahDifference);
        } elseif ($jumlahDifference < 0) {
            $this->restoreInventoryForDeletedPembeli($noSpk, abs($jumlahDifference));
        }
    }

    /**
     * Deduct grand_total_bagus from produksi database when barang keluar ke pembeli
     * 
     * @param string $noSpk - Nomor SPK
     * @param float $jumlah - Jumlah barang yang keluar ke pembeli
     */
    private function deductGrandTotalBagusFromProduksi($noSpk, $jumlah)
    {
        try {
            $noSpk = trim((string) $noSpk); // Clean up input
            $jumlah = (int)$jumlah;
            
            error_log("DEBUG: deductGrandTotalBagusFromProduksi called with noSpk='{$noSpk}', jumlah={$jumlah}");
            
            // Connect to produksi database
            $db = \Config\Database::connect('produksi');
            error_log("DEBUG: Connected to produksi database");

            // Try exact match first, then with TRIM
            $header = $db->table('produksi_headers')
                ->select('id, nomor_spk, grand_total_bagus')
                ->where('TRIM(nomor_spk)', $noSpk)
                ->where('deleted_at', null)
                ->orderBy('tanggal', 'DESC')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            error_log("DEBUG: Query result - " . ($header ? "FOUND" : "NOT FOUND"));
            if ($header) {
                error_log("DEBUG: Found record with nomor_spk='" . $header['nomor_spk'] . "', id=" . $header['id']);
            }

            if (!$header) {
                // Try without TRIM for debugging
                $allRecords = $db->table('produksi_headers')
                    ->select('id, nomor_spk, grand_total_bagus')
                    ->like('nomor_spk', $noSpk)
                    ->where('deleted_at', null)
                    ->limit(5)
                    ->get()
                    ->getResultArray();

                error_log("DEBUG: Similar records found: " . count($allRecords));
                foreach ($allRecords as $rec) {
                    error_log("DEBUG: Record: nomor_spk='" . $rec['nomor_spk'] . "'");
                }

                error_log("WARNING: SPK '{$noSpk}' not found in produksi database");
                log_message('warning', "SPK '{$noSpk}' not found in produksi database for deduction");
                return;
            }

            // Calculate new grand_total_bagus (subtract the shipped quantity)
            $newBagus = max(0, (int)$header['grand_total_bagus'] - $jumlah);
            error_log("DEBUG: Old bagus=" . $header['grand_total_bagus'] . ", new bagus={$newBagus}");

            // Update the produksi_headers record
            $result = $db->table('produksi_headers')
                ->where('id', $header['id'])
                ->update(['grand_total_bagus' => $newBagus]);

            error_log("DEBUG: Update result: " . ($result ? 'SUCCESS' : 'FAILED'));
            error_log("INFO: Deducted {$jumlah} from SPK '{$noSpk}' grand_total_bagus. New value: {$newBagus}");
            log_message('info', "Deducted {$jumlah} from SPK '{$noSpk}' grand_total_bagus. New value: {$newBagus}");
        } catch (\Exception $e) {
            // Log the error but don't throw exception - this shouldn't block the pembeli data insertion
            error_log("ERROR EXCEPTION: " . $e->getMessage() . "\n" . $e->getTraceAsString());
            log_message('error', "Error deducting grand_total_bagus for SPK '{$noSpk}': " . $e->getMessage());
        }
    }
}