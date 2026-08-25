<?php

namespace App\Models;

class MRejectType extends BaseProduksiModel
{
    protected $table            = 'reject_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'nama_reject', 'deskripsi', 'is_active'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    /**
     * Get all active reject types
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
                    ->orderBy('nama_reject', 'ASC')
                    ->findAll();
    }

    /**
     * Get reject type by ID
     */
    public function getById($id)
    {
        return $this->find($id);
    }

    /**
     * Get reject type by name
     */
    public function getByName($name)
    {
        return $this->where('nama_reject', $name)->first();
    }

    /**
     * Check if reject type exists
     */
    public function exists($name)
    {
        return $this->where('nama_reject', $name)->first() !== null;
    }

    /**
     * Create new reject type
     */
    public function createNew($name, $deskripsi = null)
    {
        if ($this->exists($name)) {
            return false;
        }

        return $this->insert([
            'nama_reject' => $name,
            'deskripsi' => $deskripsi,
            'is_active' => 1,
        ]);
    }

    /**
     * Update reject type
     */
    public function updateType($id, $data)
    {
        return $this->update($id, $data);
    }

    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        $current = $this->find($id);
        if (!$current) {
            return false;
        }

        $newStatus = $current['is_active'] == 1 ? 0 : 1;
        return $this->update($id, ['is_active' => $newStatus]);
    }

    /**
     * Delete reject type (soft delete)
     */
    public function deleteType($id)
    {
        return $this->delete($id);
    }
}
