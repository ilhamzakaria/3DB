<?php

namespace App\Models;

use CodeIgniter\Model;

class MBarangMasukDariProduksi extends Model
{
    protected $DBGroup = 'warehouse';
    protected $table = 'barang_masuk_dari_produksi';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'no_dokumen',
        'tanggal',
        'no_spk',
        'nama_produksi',
        'kode_barang',
        'nama_barang',
        'satuan',
        'jumlah_produksi',
        'jumlah_reject',
        'jumlah_diterima',
        'nama_penerima',
        'catatan',
        'status',
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';

    // Scopes
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

    public function getByNoDokumen($no_dokumen)
    {
        return $this->where('no_dokumen', $no_dokumen)->first();
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
                ->like('no_dokumen', $filters['search'])
                ->orLike('nama_barang', $filters['search'])
                ->orLike('no_spk', $filters['search'])
                ->groupEnd();
        }

        return $query->orderBy('tanggal', 'DESC')->findAll();
    }
}
