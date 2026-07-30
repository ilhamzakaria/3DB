<?php

namespace App\Models;

use CodeIgniter\Model;

class MOrowarehouse extends Model
{
    protected $DBGroup = 'warehouse';
    protected $table = 'orowarehouse';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'tanggal',
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
        'nomor_lot',
        'status',
        'catatan',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Pagination settings
    protected $perPage = 10;

    /**
     * Get data with pagination, search, and filter
     */
    public function getWithFilter($filters = [], $perPage = 10)
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

        return $query;
    }

    /**
     * Get data by SPK for autofill
     */
    public function getBySpk($spk)
    {
        return $this->where('spk', $spk)
            ->where('status !=', 'ditolak')
            ->orderBy('tanggal', 'DESC')
            ->first();
    }

    /**
     * Get summary data for a SPK
     */
    public function getSpkSummary($spk)
    {
        return $this->where('spk', $spk)
            ->selectSum('hasil_bagus')
            ->selectSum('hasil_reject')
            ->first();
    }

    /**
     * Get all unique values for filter dropdowns
     */
    public function getUniqueShifts()
    {
        return $this->select('shift')
            ->distinct()
            ->orderBy('shift', 'ASC')
            ->findAll();
    }

    public function getUniqueGrups()
    {
        return $this->select('grup')
            ->distinct()
            ->where('grup !=', null)
            ->orderBy('grup', 'ASC')
            ->findAll();
    }

    public function getUniqueOperators()
    {
        return $this->select('operator')
            ->distinct()
            ->where('operator !=', null)
            ->orderBy('operator', 'ASC')
            ->findAll();
    }
}
