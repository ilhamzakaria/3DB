<?php

namespace App\Controllers\Traits;

use App\Models\MMesinKomposisi;

trait MesinKomposisiManagementTrait
{
    public function getKomposisiJson()
    {
        $module = str_replace('mesin_', '', $this->mesinSlug());
        $model  = new MMesinKomposisi();
        $list   = $model->getAllByModule($module);

        return $this->response->setJSON([
            'success'    => true,
            'komposisi'  => $list,
        ]);
    }

    public function storeKomposisi()
    {
        $nama   = trim((string) $this->request->getPost('nama'));
        $module = str_replace('mesin_', '', $this->mesinSlug());

        if ($nama === '') {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nama komposisi tidak boleh kosong.',
            ]);
        }

        $model = new MMesinKomposisi();

        if ($model->exists($module, $nama)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => "Komposisi \"$nama\" sudah ada.",
            ]);
        }

        $model->insert(['mesin_module' => $module, 'nama' => $nama]);

        return $this->response->setJSON([
            'success'   => true,
            'message'   => "Komposisi \"$nama\" berhasil ditambahkan.",
            'komposisi' => $model->getAllByModule($module),
        ]);
    }

    public function deleteKomposisi(int $id)
    {
        $model = new MMesinKomposisi();
        $row   = $model->find($id);

        if (!$row) {
            return $this->response->setJSON(['success' => false, 'message' => 'Data tidak ditemukan.']);
        }

        $module = str_replace('mesin_', '', $this->mesinSlug());
        $model->delete($id);

        return $this->response->setJSON([
            'success'   => true,
            'message'   => 'Komposisi berhasil dihapus.',
            'komposisi' => $model->getAllByModule($module),
        ]);
    }
}
