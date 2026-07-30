<?php

namespace App\Models;

class MMesinKomposisi extends BaseGudangModel
{
    protected $table         = 'mesin_komposisi';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['mesin_module', 'nama'];
    protected $useTimestamps = true;

    /**
     * Ambil semua komposisi untuk sebuah mesin module, urut abjad.
     */
    public function getAllByModule(string $mesinModule): array
    {
        $rows = $this->where('mesin_module', $mesinModule)
                     ->orderBy('nama', 'ASC')
                     ->findAll();
        return array_column($rows, 'nama');
    }

    /**
     * Cek duplikat (case-insensitive).
     */
    public function exists(string $mesinModule, string $nama): bool
    {
        return $this->where('mesin_module', $mesinModule)
                    ->where('LOWER(nama)', strtolower($nama))
                    ->countAllResults() > 0;
    }
}
