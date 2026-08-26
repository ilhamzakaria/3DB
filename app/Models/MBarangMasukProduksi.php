<?php

namespace App\Models;

use CodeIgniter\Model;

class MBarangMasukProduksi extends Model
{
    protected $DBGroup = 'warehouse';
    protected $table      = 'barang_masuk_produksi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;

    protected $allowedFields = [
        // Field lama (renamed)
        'nomor_spk',
        'nomor_lot',
        'tanggal',
        'no_spk',
        'nama_produksi',
        'kode_barang',
        'nama_barang',
        'satuan',
        'jumlah_keluar',
        'jumlah_terima',
        'nama_penerima',
        'catatan',
        'status',
        // Field baru (detail produksi)
        'spk',
        'produk',
        'shift',
        'grup',
        'hasil_bagus',
        'hasil_reject',
        'material',
        'colorant',
        'packaging',
        'operator',
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    protected $perPage = 10;

    /**
     * Get data with pagination, search, and filter (versi baru dengan field produksi)
     */
    public function getWithFilterDetail($filters = [], $perPage = 10)
    {
        $query = $this;

        // Status filter
        if (!empty($filters['status'])) {
            $query = $query->where('status', $filters['status']);
        }

        // Date range filter
        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $query = $query->where('DATE(tanggal) >=', $filters['tanggal_dari'])
                ->where('DATE(tanggal) <=', $filters['tanggal_sampai']);
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

        // General search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query = $query->groupStart()
                ->like('spk', $search)
                ->orLike('produk', $search)
                ->orLike('operator', $search)
                ->orLike('material', $search)
                ->orLike('nomor_spk', $search)
                ->orLike('grup', $search)
                ->groupEnd();
        }

        return $query->orderBy('tanggal', 'DESC')->orderBy('id', 'DESC');
    }

    /**
     * Get unique shifts for dropdown
     */
    public function getUniqueShiftsBmp()
    {
        return $this->select('shift')
            ->distinct()
            ->where('shift IS NOT NULL')
            ->orderBy('shift', 'ASC')
            ->findAll();
    }

    /**
     * Get unique grups for dropdown
     */
    public function getUniqueGrupsBmp()
    {
        return $this->select('grup')
            ->distinct()
            ->where('grup IS NOT NULL')
            ->where('grup !=', '')
            ->orderBy('grup', 'ASC')
            ->findAll();
    }

    /**
     * Get unique operators for dropdown
     */
    public function getUniqueOperatorsBmp()
    {
        return $this->select('operator')
            ->distinct()
            ->where('operator IS NOT NULL')
            ->where('operator !=', '')
            ->orderBy('operator', 'ASC')
            ->findAll();
    }

    // ---- Legacy methods (backwards compat) ----

    public function getByStatus($status)
    {
        return $this->where('status', $status)->findAll();
    }

    public function getByTanggal($tanggal)
    {
        return $this->where('DATE(tanggal)', $tanggal)->findAll();
    }

    public function getByNoSpk($no_spk)
    {
        return $this->where('no_spk', $no_spk)->findAll();
    }

    public function getByNomorSpk($nomor_spk)
    {
        return $this->where('nomor_spk', $nomor_spk)->first();
    }

    public function getByNoDokumen($no_dokumen)
    {
        // Backward compatibility
        return $this->getByNomorSpk($no_dokumen);
    }

    public function getWithFilter($filters = [])
    {
        $query = $this;

        if (!empty($filters['status'])) {
            $query = $query->where('status', $filters['status']);
        }

        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $query = $query->where('DATE(tanggal) >=', $filters['tanggal_dari'])
                ->where('DATE(tanggal) <=', $filters['tanggal_sampai']);
        }

        if (!empty($filters['no_spk'])) {
            $query = $query->where('no_spk', $filters['no_spk']);
        }

        if (!empty($filters['search'])) {
            $query = $query->groupStart()
                ->like('nomor_spk', $filters['search'])
                ->orLike('nama_barang', $filters['search'])
                ->orLike('no_spk', $filters['search'])
                ->groupEnd();
        }

        return $query->orderBy('tanggal', 'DESC')->findAll();
    }
}
