<?php

namespace App\Models;

use CodeIgniter\Model;

class MBarangKeluarPembeli extends Model
{
    protected $DBGroup = 'warehouse';
    protected $table = 'barang_keluar_pembeli';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'nomor_lot',
        'tanggal',
        'nama_pembeli',
        'kode_barang',
        'nama_barang',
        'satuan',
        'jumlah',
        'harga_satuan',
        'total_harga',
        'no_so',
        'no_invoice',
        'alamat_pengiriman',
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

    public function getByPembeli($nama_pembeli)
    {
        return $this->like('nama_pembeli', $nama_pembeli)->findAll();
    }

    public function getByNomorLot($nomor_lot)
    {
        return $this->where('nomor_lot', $nomor_lot)->first();
    }

    public function getByNomorSpk($nomor_spk)
    {
        // Backward compatibility - search nomor_lot instead
        return $this->where('nomor_lot', $nomor_spk)->first();
    }

    public function getByNoDokumen($no_dokumen)
    {
        // Backward compatibility
        return $this->getByNomorLot($no_dokumen);
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

        if (!empty($filters['nama_pembeli'])) {
            $query = $query->like('nama_pembeli', $filters['nama_pembeli']);
        }

        if (!empty($filters['search'])) {
            $query = $query->groupStart()
                ->like('nomor_lot', $filters['search'])
                ->orLike('nama_barang', $filters['search'])
                ->orLike('nama_pembeli', $filters['search'])
                ->groupEnd();
        }

        return $query->orderBy('tanggal', 'DESC')->findAll();
    }
}
