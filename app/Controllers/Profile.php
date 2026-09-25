<?php

namespace App\Controllers;

use App\Models\MUser;

class Profile extends BaseController
{
    protected $userModel;
    protected $uploadDir = 'uploads';

    public function __construct()
    {
        $this->userModel = new MUser();
        helper(['form', 'url']);
    }

    /**
     * Halaman profile user - menampilkan form edit dan upload
     */
    public function index()
    {
        if (!session()->get('logged_in')) {
            return redirect()->to('/login');
        }

        $userId = session()->get('id_user');
        $user = $this->userModel->find($userId);

        if (!$user) {
            return redirect()->to('/login')->with('error', 'User tidak ditemukan');
        }

        return view('profile/index', [
            'title' => 'Profile Pengguna',
            'user' => $user,
            'uploadDir' => $this->uploadDir,
        ]);
    }

    /**
     * Upload foto profile
     */
    public function uploadFoto()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $file = $this->request->getFile('foto');

        // Check if file exists dan valid
        if (!$file || $file->getError() !== 0) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'File tidak ditemukan atau terjadi error upload',
            ]);
        }

        if (!$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'File tidak valid: ' . $file->getErrorString(),
            ]);
        }

        // Validasi tipe file
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP. MIME: ' . $file->getMimeType(),
            ]);
        }

        // Validasi ukuran file (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Ukuran file terlalu besar. Maksimal 5MB, dikirim: ' . ($file->getSize() / 1024 / 1024) . 'MB',
            ]);
        }

        try {
            $userId = session()->get('id_user');
            $newName = 'profile_' . $userId . '_' . time() . '.' . $file->getExtension();
            
            // Buat folder jika belum ada
            $uploadPath = WRITEPATH . '../public/' . $this->uploadDir . '/profile/';
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            // Upload file
            if (!$file->move($uploadPath, $newName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Gagal memindahkan file ke folder upload',
                ]);
            }

            // Hapus foto lama jika ada
            $user = $this->userModel->find($userId);
            if ($user && !empty($user['foto_profile'])) {
                $oldPath = WRITEPATH . '../public/' . $user['foto_profile'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Update database
            $updateResult = $this->userModel->update($userId, [
                'foto_profile' => $this->uploadDir . '/profile/' . $newName,
            ]);

            if (!$updateResult) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Gagal menyimpan data ke database',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Foto profile berhasil diupload',
                'foto_profile' => base_url($this->uploadDir . '/profile/' . $newName),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Upload tanda tangan
     */
    public function uploadTtd()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        $file = $this->request->getFile('ttd');

        // Check if file exists dan valid
        if (!$file || $file->getError() !== 0) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'File tidak ditemukan atau terjadi error upload',
            ]);
        }

        if (!$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'File tidak valid: ' . $file->getErrorString(),
            ]);
        }

        // Validasi tipe file
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Tipe file tidak didukung. Gunakan JPG, PNG, GIF, atau WebP',
            ]);
        }

        // Validasi ukuran file (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Ukuran file terlalu besar. Maksimal 5MB',
            ]);
        }

        try {
            $userId = session()->get('id_user');
            $newName = 'ttd_' . $userId . '_' . time() . '.' . $file->getExtension();
            
            // Buat folder jika belum ada (gunakan FCPATH untuk path yang reliable di hosting)
            $uploadPath = FCPATH . $this->uploadDir . '/ttd/';
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            // Upload file
            if (!$file->move($uploadPath, $newName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Gagal memindahkan file ke folder upload. Path: ' . $uploadPath . ' | Writable: ' . (is_writable($uploadPath) ? 'Ya' : 'Tidak'),
                ]);
            }

            // Hapus tanda tangan lama jika ada
            $user = $this->userModel->find($userId);
            if ($user && !empty($user['tanda_tangan'])) {
                $oldPath = FCPATH . $user['tanda_tangan'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Update database
            $updateResult = $this->userModel->update($userId, [
                'tanda_tangan' => $this->uploadDir . '/ttd/' . $newName,
            ]);

            if (!$updateResult) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Gagal menyimpan data ke database',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tanda tangan berhasil diupload',
                'tanda_tangan' => base_url($this->uploadDir . '/ttd/' . $newName),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Auto-upload ttd dari lokasi default (untuk compatibility dengan catatan.md)
     */
    public function autoUploadTtd()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Unauthorized']);
        }

        try {
            $userId = session()->get('id_user');
            $defaultTtdPath = FCPATH . 'uploads/ttd1.png';

            // Cek apakah file default ada
            if (!file_exists($defaultTtdPath)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'File tanda tangan default tidak ditemukan di: public/uploads/ttd1.png',
                ]);
            }

            // Copy file ke folder user
            $uploadPath = FCPATH . $this->uploadDir . '/ttd/';
            if (!is_dir($uploadPath)) {
                @mkdir($uploadPath, 0755, true);
            }

            $newName = 'ttd_' . $userId . '_' . time() . '.png';
            if (!@copy($defaultTtdPath, $uploadPath . $newName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Gagal menyalin file tanda tangan default',
                ]);
            }

            // Hapus tanda tangan lama jika ada
            $user = $this->userModel->find($userId);
            if ($user && !empty($user['tanda_tangan'])) {
                $oldPath = FCPATH . $user['tanda_tangan'];
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Update database
            $updateResult = $this->userModel->update($userId, [
                'tanda_tangan' => $this->uploadDir . '/ttd/' . $newName,
            ]);

            if (!$updateResult) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Gagal menyimpan data ke database',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Tanda tangan default berhasil digunakan',
                'tanda_tangan' => base_url($this->uploadDir . '/ttd/' . $newName),
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Preview foto profile
     */
    public function previewFoto($userId)
    {
        $user = $this->userModel->find($userId);

        if (!$user || !$user['foto_profile']) {
            return $this->response->setStatusCode(404);
        }

        $filePath = WRITEPATH . '../public/' . $user['foto_profile'];
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404);
        }

        return $this->response->download($filePath, null);
    }

    /**
     * Preview tanda tangan
     */
    public function previewTtd($userId)
    {
        $user = $this->userModel->find($userId);

        if (!$user || !$user['tanda_tangan']) {
            return $this->response->setStatusCode(404);
        }

        $filePath = WRITEPATH . '../public/' . $user['tanda_tangan'];
        if (!file_exists($filePath)) {
            return $this->response->setStatusCode(404);
        }

        return $this->response->download($filePath, null);
    }

    /**
     * Get current user's signature for AJAX auto-fill (form context)
     * Used in produksi form to auto-fill operator signature when shift is selected
     */
    public function getTtdForForm()
    {
        if (!session()->get('logged_in')) {
            return $this->response->setStatusCode(401)->setJSON([
                'success' => false,
                'error' => 'Unauthorized',
            ]);
        }

        try {
            $userId = session()->get('id_user');
            $user = $this->userModel->find($userId);

            if (!$user) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'User tidak ditemukan',
                ]);
            }

            // If user has a signature, return its URL
            if (!empty($user['tanda_tangan'])) {
                $filePath = WRITEPATH . '../public/' . $user['tanda_tangan'];
                if (file_exists($filePath)) {
                    return $this->response->setJSON([
                        'success' => true,
                        'tanda_tangan' => base_url($user['tanda_tangan']),
                    ]);
                }
            }

            // No signature found
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Operator belum upload tanda tangan di profile',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error: ' . $e->getMessage(),
            ]);
        }
    }
}
