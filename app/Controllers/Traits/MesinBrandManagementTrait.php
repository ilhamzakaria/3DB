<?php

namespace App\Controllers\Traits;

use App\Models\MMesinBrand;

    trait MesinBrandManagementTrait
{
    public function getBrandsJson()
    {
        $grade = trim((string) $this->request->getGet('grade'));
        $module = str_replace('mesin_', '', $this->mesinSlug());
        $model = new MMesinBrand();
        $brands = $model->getBrandsByGrade($module, $grade);

        return $this->response->setJSON([
            'success' => true,
            'brands' => array_column($brands, 'brand_name'),
        ]);
    }

    public function storeBrand()
    {
        $grade = trim((string) $this->request->getPost('grade'));
        $brand = trim((string) $this->request->getPost('brand_name'));
        $module = str_replace('mesin_', '', $this->mesinSlug());

        if ($grade === '' || $brand === '') {
            return $this->response->setJSON(['success' => false, 'message' => 'Grade dan nama brand wajib diisi.']);
        }

        $model = new MMesinBrand();

        if ($model->exists($module, $grade, $brand)) {
            return $this->response->setJSON(['success' => false, 'message' => "Brand \"$brand\" sudah ada di grade $grade."]);
        }

        $model->insert(['mesin_module' => $module, 'grade' => $grade, 'brand_name' => $brand]);

        $brands = $model->getBrandsByGrade($module, $grade);
        return $this->response->setJSON([
            'success' => true,
            'message' => "Brand \"$brand\" berhasil ditambahkan.",
            'brands' => array_column($brands, 'brand_name'),
        ]);
    }

    public function deleteBrand($id)
    {
        $model = new MMesinBrand();
        $row = $model->find($id);

        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Brand tidak ditemukan.']);
        }

        $model->delete($id);

        $brands = $model->getBrandsByGrade($row['mesin_module'], $row['grade']);
        return $this->response->setJSON([
            'success' => true,
            'message' => "Brand \"{$row['brand_name']}\" berhasil dihapus.",
            'grade' => $row['grade'],
            'brands' => array_column($brands, 'brand_name'),
        ]);
    }
}
