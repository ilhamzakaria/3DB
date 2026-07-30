<?php

namespace App\Models;

class MMesinBrand extends BaseGudangModel
{
    protected $table         = 'mesin_brands';
    protected $primaryKey    = 'id';
    protected $returnType    = 'array';
    protected $allowedFields = ['mesin_module', 'grade', 'brand_name'];
    protected $useTimestamps = true;

    /**
     * Return all brands for a mesin_module grouped by grade
     * [ 'A+' => ['Brand1', 'Brand2'], 'A' => [...], ... ]
     */
    public function getAllGroupedByGrade(string $mesinModule): array
    {
        $rows = $this->where('mesin_module', $mesinModule)
                     ->orderBy('grade')
                     ->orderBy('brand_name')
                     ->findAll();
        $result = [];
        foreach ($rows as $row) {
            $result[$row['grade']][] = $row['brand_name'];
        }
        return $result;
    }

    /**
     * Get all brands for a single mesin_module + grade
     */
    public function getBrandsByGrade(string $mesinModule, string $grade): array
    {
        return $this->where('mesin_module', $mesinModule)
                    ->where('grade', $grade)
                    ->orderBy('brand_name')
                    ->findAll();
    }

    /**
     * Check if a brand exists (case-insensitive)
     */
    public function exists(string $mesinModule, string $grade, string $brandName): bool
    {
        return $this->where('mesin_module', $mesinModule)
                    ->where('grade', $grade)
                    ->where('LOWER(brand_name)', strtolower($brandName))
                    ->countAllResults() > 0;
    }
}
