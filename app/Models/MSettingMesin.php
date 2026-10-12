<?php

namespace App\Models;

use CodeIgniter\Model;

class MSettingMesin extends Model
{
    protected $table            = 'setting_mesin';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $DBGroup          = 'master';
    protected $allowedFields    = [
        'id_user',
        'product',
        'machine',
        'type',
        'no_mc',
        'tanggal',
        'shift',
        'jam',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = false;

    public function getValidationRules(array $options = []): array
    {
        return [
            'id_user' => 'required',
            'product' => 'required',
            'machine' => 'required',
            'type'    => 'required',
            'no_mc'   => 'required',
            'tanggal' => 'required|valid_date[Y-m-d]',
            'shift'   => 'required|integer',
        ];
    }

    public function getValidationMessages(array $options = []): array
    {
        return [
            'id_user' => [
                'required' => 'ID User harus diisi',
            ],
            'product' => [
                'required' => 'Produk harus diisi',
            ],
            'machine' => [
                'required' => 'Mesin harus diisi',
            ],
            'type' => [
                'required' => 'Tipe harus diisi',
            ],
            'no_mc' => [
                'required' => 'Nomor Mesin harus diisi',
            ],
            'tanggal' => [
                'required'    => 'Tanggal harus diisi',
                'valid_date' => 'Format tanggal tidak valid (gunakan YYYY-MM-DD)',
            ],
            'shift' => [
                'required' => 'Shift harus diisi',
                'integer'  => 'Shift harus berupa angka',
            ],
        ];
    }
}
